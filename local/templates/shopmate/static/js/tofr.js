var cashDevice = function(device) {
	var defaultDevice = {
			ip: '127.0.0.1',
			port: '54321',
			onopen: function (xml) {},
			onmessage: function (event) {
				alert(event.data);
			},
			onerror: function (error) {
				console.log(error);
			}
		};

	var that = {
		device: defaultDevice,
		init: function() {
			if(typeof(device) == 'function')
			{
				device = {onmessage: device};
			}
			if(typeof(device) == 'object') {
				for (key in device) {
					that.device[key] = device[key];
				}
			}

			return that;
		},
		checkDevice: function(par) {
			var string='\<?xml version=\"1.0\" encoding=\"windows-1251\"?\>\n\<!--SHTRICH-M PROXY EXCHANGE FORMAT--\>\n\<!DOCTYPE pef\>\n\<pef version=\"1.0\"\>\n\<Request id=\"{69d7397f-a977-4dca-b4ef-75949f33e71f}\" function=\"checkDevice\"\>\n\<OperatorPass\>30\</OperatorPass\>\n\<Device LDNumber=\"'+par+'\"\/\>\n\</Request\>\n\</pef\>'
			return that.send(string, 'checkDevice');
		},
		deviceReady: function(par) {
			var string='\<?xml version=\"1.0\" encoding=\"windows-1251\"?\>\n\<!--SHTRICH-M PROXY EXCHANGE FORMAT--\>\n\<!DOCTYPE pef\>\n\<pef version=\"1.0\"\>\n\<Request id=\"{40370679-959c-4f49-834a-65e741556213}\" function=\"deviceReady\"\>\n\<OperatorPass\>30\</OperatorPass\>\n\<Device LDNumber=\"'+par+'\"\/\>\n\</Request\>\n\</pef\>'
			return that.send(string, 'deviceReady');
		},
		isDrawerOpened: function(par) {
			var string='\<?xml version=\"1.0\" encoding=\"windows-1251\"?\>\n\<!--SHTRICH-M PROXY EXCHANGE FORMAT--\>\n\<!DOCTYPE pef\>\n\<pef version=\"1.0\"\>\n\<Request id=\"{afa422d8-ae40-4611-a592-9a5942441aba}\" function=\"isDrawerOpened\"\>\n\<OperatorPass\>30\</OperatorPass\>\n\<Device LDNumber=\"'+par+'\"\/\>\n\</Request\>\n\</pef\>'
			return that.send(string, 'isDrawerOpened');
		},
		openDrawer: function(par) {
			var string='\<?xml version=\"1.0\" encoding=\"windows-1251\"?\>\n\<!--SHTRICH-M PROXY EXCHANGE FORMAT--\>\n\<!DOCTYPE pef\>\n\<pef version=\"1.0\"\>\n\<Request id=\"{4180f328-f471-4658-820c-8698777e916c}\" function=\"openDrawer\"\>\n\<OperatorPass\>30\</OperatorPass\>\n\<DrawerNumber LDNumber=\"'+par+'\"\>0\<\/DrawerNumber>\n\</Request\>\n\</pef\>'
			return that.send(string, 'openDrawer');
		},
		printReceipt: function(par) {
			var tpay;if(par[4]==0)tpay='cash';else tpay=par[4];var string='\<?xml version=\"1.0\" encoding=\"windows-1251\"?\>\n\<!--SHTRICH-M PROXY EXCHANGE FORMAT--\>\n\<!DOCTYPE pef\>\n\<pef version=\"1.0\"\>\n\<Request id=\"{2ee8cce1-ea57-41f9-b8c4-0e5b8239d5fe}\" function=\"printReceipt\"\>\n\<Device LDNumber=\"1\"\/>\n\<OperatorPass\>30\<\/OperatorPass\>\n\<ReceiptData openTray=\"yes\"\>\n\<Text align=\"center\"\>'+par[0]+'\<\/Text>\n\<Text align=\"center\"\>'+par[1]+'\<\/Text>\n'; for (var i=0; i<par[3].length; i++){string=string+'\<Item department=\"'+par[3][i][3]+'\" tax1=\"1\" quantity=\"'+par[3][i][2]+'\" price=\"'+par[3][i][1]+'\"\>'+par[3][i][0]+'\<\/Item\>\n';}string=string+'\<\/ReceiptData\>\n\<Summ type=\"'+tpay+'\"\>'+par[2]+'\<\/Summ\>\n\</Request\>\n\</pef\>'
			return that.send(string, 'printReceipt');
		},
		enumDevices: function(){
			var string='\<?xml version=\"1.0\" encoding=\"windows-1251\"?\>\n\<!--SHTRICH-M PROXY EXCHANGE FORMAT--\>\n\<!DOCTYPE pef\>\n\<pef version=\"1.0\"\>\n\<Request id=\"{73ece452-122d-4d82-af24-5e78e2f2c68b}\" function=\"enumDevices\"/\>\n\</pef\>'
			return that.send(string, 'enumDevices');
		},
		posPay: function(amm, term){
			var string='\<?xml version=\"1.0\" encoding=\"windows-1251\"?\>\n\<!--SHTRICH-M PROXY EXCHANGE FORMAT--\>\n\<!DOCTYPE pef\>\n\<pef version=\"1.0\"\>\n\<Request id=\"{7623fb4d-9144-4ef8-99d5-68c7f70014e7}\" function=\"posPay\"\>\n\<Amount\>'+amm+'\<\/Amount\>\n\<CurrencyCode\>643\<\/CurrencyCode\>\n\<OperationCode\>1\<\/OperationCode\>\n\<TerminalId\>'+term+'\<\/TerminalId\>\n\<TimeOut\>180000\<\/TimeOut\>\n\</Request\>\n\</pef\>'
			return that.send(string, 'posPay');
		},
		send: function(xml, func) {
			if(!window.WebSocket) {
				document.body.innerHTML = 'WebSocket в этом браузере не поддерживается.';
			}
			var socket = new WebSocket('ws://'+that.device.ip+':'+that.device.port);
			socket.onopen = function() {
				that.device.onopen({data: xml, funcOpen: func});
				socket.send(xml);
			};
			socket.onmessage = function(event) {
				event.funcOpen = func;
				that.device.onmessage(event);
			};
			socket.onerror = function(error) {
				that.device.onmessage(error);
			};
		}
	}
	return that.init();
}