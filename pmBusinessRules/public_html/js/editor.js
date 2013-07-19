$(document).ready(init);

var brControl;

function init()
{
    initComponents();
    atachEvents();
}

function initComponents()
{
    var defaults = {
        name: 'New Rule Set',
        type: 'single',
        moduleName: '',
        ruleset: [],
        columns: {
            conclusions: [],
            conditions: []
        }
    };

    if (ruleSet != '') {
        if (ruleSet.RstStruct) {
            ruleSetStruct = ruleSet.RstStruct;
        } else {
            ruleSetStruct = defaults;
            ruleSetStruct.name = ruleSet.RstName;
            ruleSetStruct.type = ruleSet.RstType;
            ruleSetStruct.moduleName = 'Process: ' + ruleSet.ProTitle;
        }
    } else {
        ruleSetStruct = defaults;
    }

    ruleSetStruct.fields = fields;

    console.log('loaded', ruleSetStruct);
    // console.log('sample', data1);

    brControl = $('#business-rule').BRControl(ruleSetStruct);
}


function atachEvents()
{
    $('.x-action-save').on('click', saveRule);
}

function saveRule()
{
    var data = brControl.getJSON();

    if (! data) {
        alert("Errors found on Business Rules Control");
        return false;
    }

    $.post('controllers/businessRulesProxy.php', {
        RST_UID: id,
        data: JSON.stringify(data),
        functionExecute: 'saveRule'
    }).done(function(response){
        console.log(response);
        response = jQuery.parseJSON(response);
        console.log(response);

        if (response.success) {
            alert("Rule Set saved successfully");
        } else {
            alert("ERROR: " + response.message);
        }
    });

    return false;
}

var data1 = {
    name: 'Business Rule Name',
    fields: [
        {value:'account_id', text: 'Account ID'},
        {value:'account_name', text: 'Account Name'},
        {value:'account_number', text: 'Account Number'}
    ],
    type: 'single',
    columns: {
        "conditions": [
            "account_id",
            "account_number"
        ],
            "conclusions":[
            "",
            "account_name"
        ]
    },
    ruleset: [
        {
            "id":1,
            "conditions":[
                {
                    "value":[
                        {
                            "value":"account_id",
                            "type":"VAR"
                        },{
                            "value":"x",
                            "type":"ARITMETIC"
                        },{
                            "value":"6",
                            "type":"INT"
                        }
                    ],
                    "variable_name":"account_id",
                    "condition":"<="
                },{
                    "value":[
                        {
                            "value":"NOW",
                            "type":"CONST"
                        }
                    ],
                    "variable_name":"account_number",
                    "condition":"within"
                }
            ],
            "conclusions":[
                {
                    "value":[
                        {
                            "value":"3",
                            "type":"INT"
                        }
                    ],
                    "conclusion_value":"result",
                    "conclusion_type":"return"
                },{
                    "value":[
                        {
                            "value":"account_id",
                            "type":"VAR"
                        },{
                            "value":"==",
                            "type":"EVALUATION"
                        },{
                            "value":"3",
                            "type":"INT"
                        }
                    ],
                    "conclusion_value":"account_name",
                    "conclusion_type":"variable"
                }
            ]
        }
    ],
    onChange: function(h) {
        console.log('Change', h);
    },
    onAddColumn: function () {
        console.log('Add Column');
    },
    onRemoveColumn: function () {
        console.log('Remove Column');
    }
};



