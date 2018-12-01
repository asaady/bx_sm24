create table if not exists b_sm_todo_log (
	/*SYSTEM GENERATED*/
	ID INT(18) not null auto_increment,
	TIMESTAMP_X TIMESTAMP not null,

	/*CALLER INFO*/
	STORE_ID INT(18) not null,
	SECTION_ID VARCHAR(50) not null, /*overhead, cash, personal*/
	ITEM_ID VARCHAR(255) not null, /*order id, overhead id, user id, element id*/

	/*FROM $_SERVER*/
	REMOTE_ADDR VARCHAR(40),
	USER_AGENT TEXT, /*2000 for oracle and mssql*/
	REQUEST_URI TEXT, /*2000 for oracle and mssql*/

	/*FROM CONSTANTS AND VARIABLES*/
	SITE_ID CHAR(2), /*if defined*/
	USER_ID INT(18), /*if logged in*/
	GUEST_ID INT(18), /* if statistics installed*/

	/*ADDITIONAL*/
	DESCRIPTION LONGTEXT,

	ACTIVE char(1) not null default 'Y',	
	PRIMARY KEY (ID),
	INDEX ix_b_event_log_time(TIMESTAMP_X)
);

create table if not exists b_sm_egais (
	ID INT(18) not null auto_increment,
	STORE_ID INT(18) not null,
	OPT VARCHAR(50) not null,
	REPLY_ID VARCHAR(255),
	URL VARCHAR(255) not null,
	DOCUMENT VARCHAR(255),
	DOCUMENT_NUMBER VARCHAR(255),
	XML LONGTEXT,

	PRIMARY KEY (ID)
);

create table if not exists b_sm_egais_waybill (
	ID INT(18) not null auto_increment,
	STORE_ID INT(18) not null,
	NAME varchar(255) not null,

	IDENTITY VARCHAR(255),
	DATE DATETIME NULL,
	NUMBER VARCHAR(255) not null,

	WAYBILL LONGTEXT,
	FORMBREGINFO LONGTEXT,

	ACCEPTED char(1) not null default 'N',

	PRIMARY KEY (ID)
);

create table if not exists b_sm_egais_waybill_act (
	ID INT(18) not null auto_increment,
	NAME varchar(255) not null,
	DATE DATETIME NULL,
	
	DOCUMENT VARCHAR(255),
	REPLY_ID VARCHAR(255),
	XML LONGTEXT,

	PRIMARY KEY (ID)
);

/* START DENCY PART */
create table if not exists b_sm_dnc_updated_products (
	ID INT NOT NULL AUTO_INCREMENT,
	PRODUCT_ID INT NOT NULL,
	STORE_ID INT NOT NULL,
	EXPORT CHAR(1) NOT NULL DEFAULT 'N',
	PRIMARY KEY (ID)
);

create table if not exists b_sm_dnc_cashbox_exchange_monitor (
	ID INT NOT NULL AUTO_INCREMENT,
	CASHBOX INT NOT NULL,
	SHOP INT NOT NULL,
	EXCHANGE_FLAG CHAR(1) NOT NULL DEFAULT 'N',
	CASHBOX_ANSWER TEXT,
	EXCHANGE_ERROR_FLAG CHAR(1) NOT NULL DEFAULT 'N',
	EXCHANGE_TIME DATETIME,
	PRIMARY KEY (ID)
);

create table if not exists b_sm_dnc_cashboxes (
	ID INT NOT NULL AUTO_INCREMENT,
	CASHBOX INT NOT NULL,
	SHOP INT NOT NULL,
	STATUS CHAR(5) NOT NULL DEFAULT "OFF",
	PRIMARY KEY (ID)
);
/* END DENCY PART */
