var LANG = "en";
var TRANSLATIONS_PLUGIN = {
    // List & Form
    ID_LIST_SLA: {"en": "List SLA",  "es": "Lista SLA"},
    ID_NAME: {"en": "Name", "es": "Nombre"},
    ID_DESCRIPTION: {"en": "Description", "es": "Descripción" },
    ID_PROCESS: {"en": "Process", "es": "Proceso" },
    ID_RANGE_TASKS: {"en": "Range tasks", "es": "Rango de Tareas" },
    ID_STATUS: {"en": "Status", "es": "Estado" },

    ID_NEW_SLA: {"en": "New SLA" },
    ID_EDIT_SLA: {"en": "Edit SLA" },
    ID_SLA_INFORMATION: {"en": "SLA information" },
    ID_TYPE: {"en": "Type" },
    ID_TASK_START: {"en": "Task start" },
    ID_DURATION: {"en": "Duration" },
    ID_CONDITION: {"en": "Condition" },
    ID_SLA_TYPE: {"en": "SLA type" },
    ID_RELOAD: {"en": "Re-calculate cases already entered" },
    ID_ACTIVE_PENALTY: {"en": "Activate penalty" },
    ID_PENALTY: {"en": "Penalty" },
    ID_FOR_EACH: {"en": "for each" },
    ID_EXCEED: {"en": "exceeded" },
    ID_TO: {"en": "To" },
    ID_THIS_FIELD_EMPTY: {"en": "This field is empty!" },
    ID_THIS_FIELD_TASK_EMPTY: {"en": "The field task is empty!" },
    ID_SUCCESSFULLY_SAVED: {"en": "Successfully saved" },

    // Report
    ID_FILTERS: {"en": "Filters" },
    ID_SLA: {"en": "SLA" },
    ID_SELECT_DATE: {"en": "Select date..." },
    ID_DATES: {"en": "Dates" },
    ID_BETWEEN: {"en": "Between" },
    ID_AND: {"en": "and" },
    ID_EXCEEDED: {"en": "Exceeded" },
    ID_STATUS_CASE: {"en": "Status case" },
    ID_CASES: {"en": "Cases #" },
    ID_TOTAL_CASES: {"en": "Total cases" },
    ID_TOTAL_EXCEEDED: {"en": "Total exceeded" },
    ID_AVERAGE_BY_CASE: {"en": "Average by case" },
    ID_START_DATE: {"en": "Start date" },
    ID_DUE_DATE: {"en": "Due date" },
    ID_FINISH_DATE: {"en": "Finish date" },
    ID_NO_SLA_SHOW: {"en": "No SLA to show" },
    ID_REPORT_GENERATED_ON: {"en": "Report generated on" },
    ID_GENERATE_REPORT: {"en": "Generate report" },
    ID_CLEAR_FILTERS: {"en": "Clear filters" },

    ID_TASKS: {"en": "Tasks" },
    ID_CASE: {"en": "Case #" },
    ID_DURATION_EXCEEDED: {"en": "Duration exceeded" },

    // Dashlet
    ID_TIMES_EXECUTED: {"en": "Times executed" },
    ID_TIME_EXCEEDED: {"en": "Time exceeded" },
    ID_AVERAGE_EXCEED: {"en": "Average exceeded" },

    // Variables
    ID_VARIABLES_PREFIX: {"en": "Variables cast prefix" },
    ID_VARIABLE: {"en": "Variable" },
    // ID_DESCRIPTION: {"en": "Description" },
    ID_REPLACE_VALUE_QUOTES: {"en": "Replace the value in quotes" },
    ID_Replace_VALUE_FLOAT: {"en": "Replace the value converted to float" },
    ID_REPLACE_VALUE_INTEGER: {"en": "Replace the value converted to integer" },
    ID_REPLACE_VALUE_URL_ENCODING: {"en": "Replace the value with URL encoding" },
    ID_REPLACE_VALUE_SQL_SENTENCES: {"en": "Replace the value for use in SQL sentences" },
    ID_REPLACE_VALUE_CHANGES: {"en": "Replace the value without changes" },
    ID_ALL_VARIABLES: {"en": "All Variables" },
    ID_SYSTEM: {"en": "System" },
    ID_LABEL: {"en": "Label" },

    // Report
    ID_NO_EXCEEDED: {"en": "Not Exceeded" },
    ID_EXCEEDED_LESS: {"en": "Exceeded by less than" },
    ID_EXCEEDED_MORE: {"en": "Exceeded by more than" },

    ID_ALL: {"en": "- All -" },
    ID_OPEN: {"en": "Open" },
    ID_COMPLETED: {"en": "Completed" },
    ID_NO_REPORT_SHOW: {"en": "No report show" },
    ID_PROBLEM_OCCURRED: {"en": "Some problem occurred" },

    ID_HOURS: { "en" : "Hours"},
    ID_DAYS: { "en" : "Days"},

    ID_ALL2: { "en" : "All"},
    ID_ACTIVE: { "en" : "Active"},
    ID_INACTIVE: { "en" : "Inactive"},
    ID_ENTIRE_PROCESS: { "en" : "Entire Process"},
    ID_MULTIPLE_TASKS: { "en" : "Multiple Tasks"},
    ID_TASK: { "en" : "Task"},
    ID_SUS: { "en" : "$us"},
    ID_POINTS: { "en" : "Points"},

    ID_ERROR: { "en" : "Error"},
    ID_MSG_DELETE_SLA: { "en" : "Do you want to delete this SLA"},
    ID_TITLE_PMSLA: { "en" : "pmSLA"},
    ID_REGISTER_DELETED: { "en" : "The register was Deleted"},
    ID_MSG_NOT_DELETE_SLA_REGISTER: { "en" : "Could not delete the SLA Register"},
    ID_FIND_RELATION: { "en" : "Find Relation..."},
    ID_GREATER_THAN: { "en" : "Greater than"},
    ID_GREATER_EQUAL_THAN: { "en" : "Greater or equal than"},
    ID_LESS_THAN: { "en" : "Less than"},
    ID_LESS_EQUAL_THAN: { "en" : "Less or equal than"},
    ID_SLA_SUMMARY: { "en" : "SLA Summary"},
    ID_UNASSIGNED: { "en" : "It hasn't finished yet"},
    ID_WAITING_START: { "en" : "It hasn't started yet"},
    ID_NOT_APPLY: { "en" : "The SLA doesn't apply."},
    ID_CLOSED: { "en" : "Closed"},
    ID_IN_PROGRESS: { "en" : "In progress"},
    ID_SIZE: { "en" : "size"}

};

var ID_PAGESIZE = 20;

function _TRANS(label)
{
    if (typeof TRANSLATIONS_PLUGIN != 'undefined'
        && typeof TRANSLATIONS_PLUGIN[label] != 'undefined'
        && typeof TRANSLATIONS_PLUGIN[label][LANG] != 'undefined') {
        trans = TRANSLATIONS_PLUGIN[label][LANG];
    } else {
        trans = "**" + label + "**";
    }
    return trans;
}

