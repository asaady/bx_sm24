<?
namespace Yadadya\Shopmate;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Loader
{
	public function addEvents($moduleName, array $arEvents)
	{
		self::removeEvents($moduleName);
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		foreach ($arEvents as $arEvent) 
			$eventManager->registerEventHandler($arEvent["FROM_MODULE_ID"], $arEvent["MESSAGE_ID"], $moduleName, $arEvent["TO_CLASS"], $arEvent["TO_METHOD"]);
	}

	public function removeEvents($moduleName)
	{
		$con = \Bitrix\Main\Application::getConnection();
		$sqlHelper = $con->getSqlHelper();

		$strSql = "DELETE FROM b_module_to_module WHERE FROM_MODULE_ID='".$sqlHelper->forSql($moduleName)."'";
		
		$con->queryExecute($strSql);
	}

	public function createDbTables($moduleName, array $arInternals)
	{
		$documentRoot = rtrim($_SERVER["DOCUMENT_ROOT"], "/\\");
		$modulePath = "/modules/".$moduleName."/";

		foreach ($arInternals as $class => $path) 
		{
			if(file_exists($documentRoot."/local".$modulePath.$path))
				require_once($documentRoot."/local".$modulePath.$path);
			elseif(file_exists($documentRoot."/bitrix".$modulePath.$path))
				require_once($documentRoot."/bitrix".$modulePath.$path);
			else
				continue;

			$entity = $class::getEntity();

			$fields = $entity->getScalarFields();
			$connection = $entity->getConnection();
			$primary = $entity->getPrimaryArray();

			$autoincrement = array();

			foreach ($fields as $field)
			{
				if ($field->isAutocomplete())
				{
					$autoincrement[] = $field->getColumnName();
				}
			}

			$sql = 'CREATE TABLE IF NOT EXISTS '.$connection->getSqlHelper()->quote($entity->getDBTableName()).' (';
			$sqlFields = array();

			foreach ($fields as $columnName => $field)
			{
				$sqlFields[$field->getColumnName()] = $connection->getSqlHelper()->quote($field->getColumnName())
					. ' ' . self::getColumnTypeByField($field)
					. ($field->isRequired() || $field->isPrimary() ? ' NOT NULL' : '')
					. ($field->getDefaultValue() ? ' DEFAULT ' . self::getDefaultValueToDB($field) : '')
					. ($field->isAutocomplete() ? ' AUTO_INCREMENT' : '')
				;
			}

			$sql .= join(', ', $sqlFields);

			if (!empty($primary))
			{
				foreach ($primary as &$primaryColumn)
				{
					$realColumnName = $fields[$primaryColumn]->getColumnName();
					$primaryColumn = $connection->getSqlHelper()->quote($realColumnName);
				}

				$sql .= ', PRIMARY KEY('.join(', ', $primary).')';
			}

			$sql .= ')' . $connection->getSqlHelper()->getQueryDelimiter();

			$connection->query($sql);

			// миграции
			$newFields = $sqlFields;
			$curFields = array(); // текущие поля
			$res = $connection->query('SHOW COLUMNS FROM '.$connection->getSqlHelper()->quote($entity->getDBTableName()));
			while($row = $res->Fetch())
			{
				$curFields[$row["Field"]] = $connection->getSqlHelper()->quote($row["Field"])
				. ' ' . $row["Type"]
				. (strtoupper($row["Null"]) ==  'NO' || strtoupper($row["Key"]) ==  'PRI' ? ' NOT NULL' : '')
				. ($row["Default"] != NULL ? ' DEFAULT \'' . $connection->getSqlHelper()->forSql($row["Default"]) . '\'' : '')
				. (stripos($row["Extra"], 'auto_increment') !==  false ? ' AUTO_INCREMENT' : '');
			}

			$pos = "FIRST";
			foreach ($newFields as $fieldName => $fieldType) 
			{
				if (empty($curFields[$fieldName]))
					$connection->query("ALTER TABLE " . $connection->getSqlHelper()->quote($entity->getDBTableName()) . " ADD COLUMN " . $fieldType . " " . $pos);
				elseif ($fieldType != $curFields[$fieldName])
				{
					$connection->query("ALTER TABLE " . $connection->getSqlHelper()->quote($entity->getDBTableName()) . " CHANGE " . $connection->getSqlHelper()->quote($fieldName) . " " . $fieldType);
				}
				$pos = "AFTER " . $connection->getSqlHelper()->quote($fieldName);
				unset($curFields[$fieldName]);
			}
			foreach ($curFields as $fieldName => $fieldType) 
			{
				$connection->query("ALTER TABLE " . $connection->getSqlHelper()->quote($entity->getDBTableName()) . " DROP COLUMN " . $connection->getSqlHelper()->quote($fieldName));
			}
		}
	}

	public function getColumnTypeByField(Entity\ScalarField $field)
	{
		$entity = $field->getEntity();
		

		$className = $entity->getDataClass();
		$fieldsMap = $className::getMap();
		if (!empty($fieldsMap[$field->getColumnName()]["column_type"]))
			return $fieldsMap[$field->getColumnName()]["column_type"];

		$connection = $entity->getConnection();
		$columnType = $connection->getSqlHelper()->getColumnTypeByField($field);

		if (strtolower($columnType) == "int")
			return "int(11)";
		elseif (strtolower($columnType) == "varchar(1)")
			return "char(1)";

		return $columnType;
	}

	public function getDefaultValueToDB(Entity\ScalarField $field)
	{
		$entity = $field->getEntity();
		

		$className = $entity->getDataClass();
		$fieldsMap = $className::getMap();
		if (!empty($fieldsMap[$field->getColumnName()]["default"]))
			return $fieldsMap[$field->getColumnName()]["default"];

		$connection = $entity->getConnection();
		return $connection->getSqlHelper()->convertToDb($field->getDefaultValue(), $field);
	}
}