<?php
/**
  @mainpage Overview
  @dontinclude overview.php

  @section Introduction

  Actions by Email Plugin es una extensi&oacute;n que permite la derivaci&oacute;n de casos por email en un determinado proceso .
  
  @subpage PAGE_INSTALLATION_CONFIGURATION
  - @ref SEC_ACTIONSBYEMAIL_OPTION
  
  @subpage PAGE_EXAMPLE
  - @ref SEC_PROCESS_CREATING
  - @ref SEC_DYNAFORMGRID
  - @ref SEC_CONFIGURATION
  - @ref SEC_CASE_RUNNING

  @subpage PAGE_REQUERIMENTS
  - @ref SEC_PROCESSMAKER_VERSION

  @subpage PAGE_GLOSARY
*/

/**
  @page PAGE_INSTALLATION_CONFIGURATION Installation and Configuration
  
  - Import the Action by Email plugin on the ADMIN tab. 
  - Enabled the Plugin.
  
  @section SEC_ACTIONSBYEMAIL_OPTION Actions by Email Option
  
  Una vez habilitado el plugin en propiedades de tarea se podra ver este tab donde se ingresar la informaci&oacute;n
  necesaria para el uso del plugin.
  
  <b>Nota</b>
  
  Se debe verificar que en el tab <b>Notifications</b> que la opcion de <b>After routing notify the next assigned user(s).</b>
  no este tiqueada, si estuviera el mensaje o template esten llenos y no vacios.
  
  
  @image html CaseActionsByEmail.png "Actions by Email Option"
  
  <b>Configuration Actions by Email</b>
  
  Se procede a llenar el tab ActionsByEmail con las siguientes consideraciones:
  
  @image html ProcessActions1.png "Configuration ActionsByEmail"
  
    1.  <b>Type</b>, se selecciona la forma en la que se enviaran los datos:
      - <b>Link to fill a form</b>, enviara todo el formulario a el correo para ser llenado .
      - <b>Use  a field to generate actions links</b>, generara link de selecci&oacute;n de acuerdo al campo q se elija.
      
    2.  <b>Template</b>, selecciona el modelo de email que se enviara, se tiene uno por defecto q es actionsByEmail.html,
        a su vez tambien posee la opcion de editar el template con el link edit.
        
    3.  <b>Dynaform</b>, seleccionas uno de los formularios que ya tengan creados.
    
    4.  <b>Field with the email</b>, es el campo donde se tendra el email que se debe seleccionar obligatoriamente.
    
    5.  <b>Field to Send in the Email</b>, esta opci&oacute;n solo se habilita cuando Type esta en Use a field to generate actions link,
        aqui seleccionas el campo que se enviara en el email.
        
    6.  <b>Register a case note</b>, es la opci&oacute;n para recibir o no emails de aviso cuando se escribe un Case Notes en el proceso.

    
    Finalmente solo presionas Apply Changes par poder guardar o modificar tu configuraci&oacute;n.
  
  
*/

/**
  @page PAGE_EXAMPLE Example
  
  @section SEC_PROCESS_CREATING Creating a Process
  
  El proceso que se describe a continuación trata de mostrar el uso de este plugin.
  
  Se debe resaltar que este plugin no funciona con Routing Rules Selection (donde el usuario asignado puede seleccionar
  manualmente la tarea siguiente)
  
  El proceso de ejemplo contempla cuatro tareas, como muestra el grafico.
  
  @image html Process1.png "Creating a Process"
  
  @section SEC_DYNAFORMGRID DynaForm 

  
  Teniendo formularios ya dise&ntilde;ados
  
  @image html ProcessFrm1.png "Dynaform ABE.Form1 - Vista"
  
  @section SEC_CONFIGURATION Configuration Actions by Email
  
  Se procede a llenar el tab ActionsByEmail con las siguientes consideraciones:
  
  @image html ProcessActions1.png "Configuration ActionsByEmail"
  
    1.  <b>Type</b>, se selecciona la forma en la que se enviaran los datos:
      - <b>Link to fill a form</b>, enviara todo el formulario a el correo para ser llenado .
      - <b>Use  a field to generate actions links</b>, generara link de selecci&oacute;n de acuerdo al campo q se elija.
      
    2.  <b>Template</b>, selecciona el modelo de email que se enviara, se tiene uno por defecto q es actionsByEmail.html,
        a su vez tambien posee la opcion de editar el template con el link edit.
        
    3.  <b>Dynaform</b>, seleccionas uno de los formularios que ya tengan creados.
    
    4.  <b>Field with the email</b>, es el campo donde se tendra el email que se debe seleccionar obligatoriamente.
    
    5.  <b>Field to Send in the Email</b>, esta opci&oacute;n solo se habilita cuando Type esta en Use a field to generate actions link,
        aqui seleccionas el campo que se enviara en el email.
        
    6.  <b>Register a case note</b>, es la opci&oacute;n para recibir o no emails de aviso cuando se escribe un Case Notes en el proceso.

    
    Finalmente solo presionas Apply Changes par poder guardar o modificar tu configuraci&oacute;n.
  
  
  @image html ProcessActions2.png "Save configuration ActionsByEmail"
  
  
  @section SEC_CASE_RUNNING Case Running
  
  Bien al iniciar un nuevo caso tenemos lo siguiente:
  
  Recordemos la configuraci&oacute;n para Task1 en este caso para la opci&oacute;n <b>Link to fill a form</b>:
  
  @image html ConfigurationTask1.png "Configuration Task1"
  
  en el correo que llega se debe hacer click en Please fill this form, que nos enviara al formulario para poder llenarlo.
  
  @image html emailTask1.png "Email Task1"
  
  @image html FormTask1.png "Form Task1"
  
  @image html ResponseTask1.png "Response Task1"
  
  Al haber habilitado Register Case Note le llegara otro email
  
  @image html CaseNoteTask1.png "Case Note Task1"
  
  Para verificar esto
  
  @image html VerifyTask1.png "Verify Task1"
  
  Ahora veamos para el caso de <b>Use  a field to generate actions links</b>
  
  Recordemos la configuraci&oacute;n para Task2, donde ponemos el campo Seleccione pais.
  
  @image html ConfigurationTask2.png "Configuration Task2"
  
  ahora en el correo se debe seleccionar una de la opciones q se muestra.
  
  @image html emailTask2.png "Email Task2"
  
  @image html ResponseTask2.png "Response Task2"
  
  Para verificar 
  
  @image html VerifyTask2.png "Verify Task2"
  
*/

/**
  @page PAGE_REQUERIMENTS Requeriments
  
  @section SEC_PROCESSMAKER_VERSION ProcessMaker Requirements

  ProcessMaker V 2.0.37 and later.
*/


/**
  @page PAGE_GLOSARY Glosary
*/