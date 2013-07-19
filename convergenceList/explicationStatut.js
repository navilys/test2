

Ext.onReady(function(){

    new Ext.Panel({
            renderTo: 'messageInfo',
            bodyBorder: false,
            bodyStyle: "background-color:#f1f1f1;",
            border: false,
            height: parseInt(ADAPTIVEHEIGHT),
            padding: 10,
            html: MESSAGEINFO
        });
    

});