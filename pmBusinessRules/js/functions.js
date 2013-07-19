var w;

function redirectPage(page){
    window.location = page;
}

function uploadFileXls () {
    w = new Ext.Window({
        title:'Upload Excel File',
        width:390,
        id: 'windowShow',
        height:110,
        modal:true,
        autoScroll:false,
        maximizable:false,
        resizable:false,
        items:[
            new Ext.FormPanel({
                id:'uploader',
                fileUpload:true,
                autoWidth: true,
                frame:true,
                autoHeight:false,
                bodyStyle:'padding: 10px 10px 0 10px;',
                labelWidth:50,
                defaults:{anchor:'90%',allowBlank:false,msgTarget:'side'},
                items:[{
                    xtype:'fileuploadfield',
                    id:'fileExcel',
                    fieldLabel:_('ID_FILE'),
                    name:'form[FILENAME]',
                    buttonText:'',
                    buttonCfg:{iconCls:'upload-icon'}
                }],
                
                buttons:[
                    {
                    text:_('ID_UPLOAD'),
                    handler:function(){
                        var uploader=Ext.getCmp('uploader');
                        if(uploader.getForm().isValid()){
                            uploader.getForm().submit({
                                url:'controllers/businessRulesProxy',
                                params: {
                                    'functionExecute': 'uploadExcelFile'
                                },
                                waitMsg:_('ID_UPLOADING_FILE'),
                                success:function(o,resp){
                                    var result=Ext.util.JSON.decode(resp.response.responseText);
                                    if(result.success){
                                        PMExt.notify('Information','Upload excel file');
                                    } else {
                                        win = new Ext.Window({applyTo:'hello-win',layout:'fit',width:500,height:300,closeAction:'hide',plain:true,html:'<h3>'+_('ID_IMPORTING_ERROR')+'</h3>'+result.message,items:[],buttons:[{text:'Close',handler:function(){win.hide();}}]});
                                        win.show(this);
                                    }
                                    Ext.getCmp('windowShow').close();
                                    storeExcelFiles.load();
                                    storePmrlFiles.load();
                                },
                                failure:function(o, resp){
                                    console.log(o);
                                    console.log(resp);
                                }
                            });
                        }
                    }
                },{
                    text:_('ID_CANCEL'),
                    handler:function(){
                        w.close();
                    }
                }
            ]})
        ]
    });
    w.show();
/*
    document.getElementById('fileExcel-file').onchange = function() {
        onselectfile('fileExcel-file', 'extensionExcelFile', 'sizeExcelFile');
    }
*/
}

function uploadFilePmrl () {
    w = new Ext.Window({
        title:'Upload Pmrl File',
        width:390,
        id: 'windowShow',
        height:110,
        modal:true,
        autoScroll:false,
        maximizable:false,
        resizable:false,
        items:[
            new Ext.FormPanel({
                id:'uploader',
                fileUpload:true,
                autoWidth: true,
                frame:true,
                autoHeight:false,
                bodyStyle:'padding: 10px 10px 0 10px;',
                labelWidth:50,
                defaults:{anchor:'90%',allowBlank:false,msgTarget:'side'},
                items:[{
                    xtype:'fileuploadfield',
                    id:'filePmrl',
                    fieldLabel:_('ID_FILE'),
                    name:'form[FILENAME]',
                    buttonText:'',
                    buttonCfg:{iconCls:'upload-icon'}
                }],
                
                buttons:[
                    {
                    text:_('ID_UPLOAD'),
                    handler:function(){
                        var uploader=Ext.getCmp('uploader');
                        if(uploader.getForm().isValid()){
                            uploader.getForm().submit({
                                url:'controllers/businessRulesProxy?functionExecute=uploadPmrlFile',
                                waitMsg:_('ID_UPLOADING_FILE'),
                                success:function(o,resp){
                                    var result=Ext.util.JSON.decode(resp.response.responseText);
                                    if(result.success){
                                        PMExt.notify('Information','Upload pmrl file');
                                    } else {
                                        win = new Ext.Window({applyTo:'hello-win',layout:'fit',width:500,height:300,closeAction:'hide',plain:true,html:'<h3>'+_('ID_IMPORTING_ERROR')+'</h3>'+result.message,items:[],buttons:[{text:'Close',handler:function(){win.hide();}}]});
                                        win.show(this);
                                    }
                                    Ext.getCmp('windowShow').close();
                                    storePmrlFiles.load();
                                },
                                failure:function(o,resp){
                                    console.log(o);
                                    console.log(resp);
                                }
                            });
                        }
                    }
                },{
                    text:_('ID_CANCEL'),
                    handler:function(){
                        w.close();
                    }
                }
            ]})
        ]
    });
    w.show();
/*
    document.getElementById('filePmrl-file').onchange = function() {
        onselectfile('filePmrl-file', 'extensionPmrlFile', 'sizePmrlFile');
    }
*/
}




function onselectfile(controlid, extFile, sizeFile) {    
    var filevalue = document.getElementById(controlid).value;
    var filename = filevalue.substr(filevalue.lastIndexOf("\\") + 1); 
    var extension = "";
    if(filename.lastIndexOf(".") != - 1)
        extension = filename.substr(filename.lastIndexOf(".") + 1).toLowerCase();    
    var extensions= document.getElementById(extFile).value.toLowerCase();
    if(extensions.length>1 && extensions.lastIndexOf('|'+extension+'|')==-1)
    {
        var ext = extensions.split('|');
        var message = "Solo puede subir archivos con extensiones";
        var j=0;
        for(i=0;i<ext.length;i++)
        {
           if(ext[i]!="")
           {
                message += " *." + ext[i];
                if(i<ext.length - 1 && ext[i+1]!="")
                    message += ",";
           } 
        }
        message += "";
        alert(message);
        __uploader_reset(controlid);
        return;
    }    
    var maxSizeBytes = document.getElementById(sizeFile).value;    
    var checked = false;
    if(document.getElementById(controlid).files != null)
    {
        var file = document.getElementById(controlid).files[0];
        if (file.fileSize)
           thisFileSize = file.fileSize;
        else thisFileSize = file.size;
        if(thisFileSize != undefined)
        {
            if(thisFileSize > maxSizeBytes)
            {
                alert('El archivo es demasiado grande ('+__uploader_convert(thisFileSize, maxSize)+') . El maximo tañamo es de '+maxSize+'.');
                __uploader_reset(controlid);
                return;
            }
            checked = true;
        }
    }
    if (!checked)
    {
        try
        {
            var myFSO = new ActiveXObject('Scripting.FileSystemObject');
            var thefile = myFSO.getFile(filevalue);
            var thisFileSize = thefile.size;
            if(thisFileSize > maxSizeBytes)
            {
                alert('El archivo es demasiado grande ('+__uploader_convert(thisFileSize, maxSize)+') . El maximo tañamo es de '+maxSize+'.');
                __uploader_reset(controlid);
                return;
            }
        }
        catch(e) {
            alert('No se puede controlar el tamaño del archivo debido que su navegador no tiene habilitada las siguientes propiedades.\n\nHerramientas->Opiones de Internet->Seguridad->Nivel Personalizado->Iniciar y generar scripts de los controles ActiveX no marcadas como seguros para scripts.\n\nHerramientas->Opiones de Internet->Seguridad->Nivel Personalizado->Incluir la ruta de acceso al directorio local cuando se cargen archivos a un servidor.\n\nEl sistema no puede controlar el tamaño del archivo (Maximo 500 kb)');
        }
    }
    return true;
    
}

function __uploader_convert(thisFileSize, maxSize)
{
    if(maxSize.substring(maxSize.length-2)=="kb"||maxSize.substring(maxSize.length-1)=="k")
    {
        return Math.round(thisFileSize / 1024.0 * 100) / 100 + "kb";
    }
    if(maxSize.substring(maxSize.length-2)=="mb"||maxSize.substring(maxSize.length-1)=="m")
    {
        return Math.round(thisFileSize / 1024.0 / 1024.0 * 100) / 100 + "mb";
    }
    return thisFileSize;
}

function __uploader_reset(controlid)
{
    w.close();
}