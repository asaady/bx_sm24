On Error Resume Next
NameScript = WScript.ScriptFullName
With WScript.CreateObject("WScript.Shell")
	RegScript = .RegRead("HKEY_CURRENT_USER\SOFTWARE\Microsoft\Windows\CurrentVersion\Run\EGAISScript")
	AddReg = True
	If Err.Number <> 0 Then
		Err.Clear
	ElseIf RegScript = NameScript Then
		AddReg = False
	End If
	If AddReg Then
		.RegWrite "HKEY_CURRENT_USER\SOFTWARE\Microsoft\Windows\CurrentVersion\Run\EGAISScript", NameScript, "REG_SZ"
		.Run "%systemroot%\System32\RUNDLL32.EXE user32.dll, UpdatePerUserSystemParameters", 0, True
	End If
End With 

Set objFSO = CreateObject("Scripting.FileSystemObject")
Set objFile = objFSO.GetFile(NameScript)
DirScript = objFSO.GetParentFolderName(objFile) 

Set xml = CreateObject("Msxml2.DOMDocument.6.0")
xml.Load "conf.txt"
If xml.parseError.errorCode <> 0 Then
	WScript.Echo "Отсутствует файл конфигурации."
Else
	Set siteList = xml.documentElement.selectNodes(".//site")
	site = siteList(0).text
	Link = "http://" + site + "/egais/"
	Set shopList = xml.documentElement.selectNodes(".//shop")
	shop = shopList(0).text
	If shop > 0 Then
		Do
			Set WshShell = CreateObject("WScript.Shell")

			WshShell.Run "curl -X GET " + Link + "?shop=" + shop + "&action=del_list -o egais_opt.txt", 0, True
			
			xml.Load "egais_opt.txt"
			Set UrlList = xml.documentElement.selectNodes(".//url")
			For each Url in UrlList
				TextString = "curl -X DELETE " + Url.text
				WshShell.Run TextString, 0, False
			Next

			WshShell.Run "curl -X DELETE " + Link + "?shop=" + shop + "&action=del_list", 0, False

			WshShell.Sleep 3600000
		Loop While 1 = 1
	Else
		WScript.Echo "Пожалуйста, скачайте файл конфигурации и загрузите его в директорию с установленной программой (" + DirScript + ")."
	End If
End If