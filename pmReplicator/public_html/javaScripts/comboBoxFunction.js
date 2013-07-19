/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($){
    var processData={};
    $.fn.ComboBoxLoad=function(options,fnExecToDo){
        /**
         * this function will load data with an ajax call; the the page direction to be 
         * called is inside options.sUrl given by the user the moment it initialices the combo box call
         * the ajax result will be stored inside a memory buffer.
         * 
         * @param oActualDom: dom object that contains the current input select
         */
        function load(oActualDom){
           if (!isDataInBuffer()){
                $.post(options.sUrl,prepareParameters(),function(data){
                    addDataToBuffer(data)
                    fillData(oActualDom);
                    if (typeof fnExecToDo!='undefined')
                        fnExecToDo.call(oActualDom);
                },"json");
            } else {
               fillData(oActualDom);
               if (typeof fnExecToDo!='undefined')
                fnExecToDo.call(oActualDom);
            }
            
        }
        /**
         * This function will create the option tag with all the needed data stored inside the buffer
         * 
         * @param oActualDom: dom object that contains the current input select
         */
        function fillData(oActualDom){
            $.each(getDataFromBuffer(),function(sKey,sValue){
               oActualDom.append('<option value="'+sKey+'">'+sValue+'</option>'); 
            });
        }
        /**
         * This Function will prepare parameters to be send to the ajax call
         */
        function prepareParameters(){
            var parameters={}
            parameters.sLoad=options.sLoad;
            if (options.hasOwnProperty("sWorkspace"))
                parameters.sWorkspace=options.sWorkspace;
            return parameters;
        }
        /**
         * verify if data exists inside buffer for a seted workspace in options.sWorkspace
         */
        function isDataInBuffer(){
            var sElementToSearch = options.hasOwnProperty("sWorkspace") ? options.sWorkspace : options.sLoad;
            return processData.hasOwnProperty(sElementToSearch);
        }
        /**
         * get data from buffer for a seted workspace in options.sWorkspace
         */
        function getDataFromBuffer(){
            var sElementToSearch = options.hasOwnProperty("sWorkspace") ? options.sWorkspace : options.sLoad;
            return processData[sElementToSearch];
        }
        /**
         * add data to buffer
         */
        function addDataToBuffer(aData){
            var sElementToSearch = options.hasOwnProperty("sWorkspace") ? options.sWorkspace : options.sLoad;
            processData[sElementToSearch]=aData;
        }
        return this.each(function(){
            if(typeof replicatorDefaultData!='undefined'){
                var aNewArray={};
                $.each(replicatorDefaultData.aListOfWorkspaces,function(key,value){
                    aNewArray[value]=value;
                 
                });
                addDataToBuffer(aNewArray);
            }
            var jDomElement=$(this);
            load(jDomElement);
        });
    };
})(jQuery);
