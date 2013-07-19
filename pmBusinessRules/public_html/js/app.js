jQuery(document).ready(function(){
	var rc = new RestClient();
    $("#form").click(function() {
        SUGAR_URL = 'http://localhost';

        combo_teams = new ComboboxField({
            jtype: 'combobox',
            label: 'Assign to team',
            name: 'act_assign_team',
            submit: true,
            //change: hiddenUpdateFn,
            // proxy: new RestProxy({
            //     url: SUGAR_URL + '/rest/v10/CrmData/teams/',
            //     restClient: rc,
            //     uid: 'public',
            //     callback: null
            // // })
        });

        combo_method = new ComboboxField({
            jtype: 'combobox',
            name: 'act_assignment_method',
            label: 'Assignment Method',
            options: [
                {text: 'Round Robin', value: 'balanced'},
                {text: 'Self Service', value: 'selfservice'}//,
                //{text: 'Static Assignment', value: 'static'}

            ],
            initialValue: 'balanced',
            required: true
        });
        proxy = new RestProxy({
            url: SUGAR_URL + '/rest/v10/ActivityDefinition/',
            restClient: rc,
            uid: '20',
            callback: null
        });

        text_y = new TextField({
            name: 'act_assignment_text',
            label: 'Assignment Method',
            initialValue: 'aa',
            required: true
        });

        items = [combo_method, combo_teams, text_y];

        callback = null;

        f = new Form({
            proxy: null, //proxy,
            items: items,
            closeContainerOnSubmit: true,
            buttons: [
                { jtype: 'submit', caption: 'Save' },
                { jtype: 'normal', caption: 'Close', handler: function () {
                    w.close();
                }}
            ],
            callback: {
                'submit': function (data) {
                    console.log(data);
                }
            }
        });

        w = new Window({
            width: 800,
            height: 400,
            modal: true,
            title: 'Activity: ' 
        });
        w.addPanel(f);
        w.show();
    });	
	
});