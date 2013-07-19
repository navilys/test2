<?php
/**
  @mainpage Overview
  @dontinclude overview.php

  @section Introduction
  
  El Plugin External Registration publica un link con el cual los usuarios pueden ingresar a un formulario de registro.
  El formulario que ha sido generado puede ser publicado en cualquier lugar por ejemplo, un sitio web. Solo el Admin User,
  que tiene el permiso PM_USERS en su rol, puede crear este formulario.

  @subpage PAGE_INSTALLATION_CONFIGURATION
  - @ref SEC_EXTERNAL_REGISTRATION_OPTION
  
  @subpage PAGE_EXAMPLE
  - @ref SEC_PROCESS_CREATING
  - @ref SEC_NEW_EXTERNAL_REGISTRATION
  - @ref SEC_LIST
  - @ref SEC_CREATE_USER
  - @ref SEC_ACTIVATING_ACCOUNT

  @subpage PAGE_REQUERIMENTS
  - @ref SEC_PROCESSMAKER_VERSION
  - @ref SEC_PROCESSMAKER_COMPATIBILITY

  @subpage PAGE_GLOSARY
*/

/**
  @page PAGE_INSTALLATION_CONFIGURATION Installation and Configuration
  
  - Import the External Registration plugin on the ADMIN tab. 
  - Enabled the Plugin.
  
  @section SEC_EXTERNAL_REGISTRATION_OPTION External Registration Option
  
  El plugin External Registration no necesita configuración adicional en el servidor. Además se tiene dos PM Function
  que permiten a ProcessMaker enviar la activación del usuario. Son externalRegistrationSendEmail () y
  getExternalRegistrationLink ()
  
  <b>Cómo funciona el Plugin</b>

  <b>Administrador</b>
  <UL type = square >
  <LI> El administrador necesita crear un formulario de registro externo que servira para realizar las peticiones de
  una nueva cuenta de usuario en ProcessMaker.
  
  <LI> El administrador tiene que configurar el comportamiento del plugin eligiendo las opciones en el
  formulario al momento de crear un nuevo registro externo .
  
  <LI> Cuando el formulario de inscripción externa es creado, se publicará tambien en la web, esta opción está
  disponible haciendo clic en View Form.
  </UL>
  
  <b>Usuario final</b>
  <UL type = square >
  <LI> Incluye el enlace correspondiente en la página web.
  
  <LI> El usuario debe llenar el formulario con el fin de crear su cuenta.
  
  <LI> Se envía un mensaje al usuario con el fin de activar la cuenta.
  
  <LI> Cuando la cuenta está activa, el usuario puede acceder al sistema en ProcessMaker con la información enviada
  en el email. 
  </UL>
*/

/**
  @page PAGE_EXAMPLE Example
  
  @section SEC_PROCESS_CREATING Creating a Process
  
  El proceso que se describe a continuación muestra el uso de este plugin.
  
  Para crear un formulario de registro externo vaya a <b>Admin> Plugins> External Registration</b>.
  
  @image html ExternalRegistrationPlugin.png "Ingreso a External Registration"
  
  Al hacer clic en esta opción, se muestra una listado con la información de todos los External Registration Form creados.
  
  @image html ExternalRegistrationList.png "Listado General"
  
  @section SEC_NEW_EXTERNAL_REGISTRATION New External Registration Form 
  
  Para crear un External Registration Form, haga clic en la opción superior de la lista y se muestra el siguiente formulario:
  
  @image html ExternalRegistrationFormCreate.png "New External Registration Form"
  
  Donde:

  <b>General</b>
  
  <UL type = square>
  <LI> <b>Title:</b> Ingrese un título breve, como una descripción principal, para el nuevo formulario de inscripción externa.
  
  <LI> <b>Use the resources from process:</b> Seleccione un proceso de la lista. El proceso seleccionardo mostrara una
  lista de DynaForm disponibles, el  Dynaform seleccionado será enviado como un enlace externo que será publicado para
  el usuario: 
  
  @image html ExternalRegistrationFormProcess.png
  
  @image html ExternalRegistrationFormDynaform.png "Informacion General"
  
  <LI> <b>E-Mail Template:</b> Elija el Template con el cual será enviado el email. El plugin se instala con
  una plantilla predeterminada llamada externalRegistration.html. Para añadir más plantillas, consulte esta
   <a href="http://wiki.processmaker.com/index.php/Process_Files_Manager" target= _blank>documentación</a>.
  
  <LI> <b>Additional DynaForm:</b> Una lista de Dynaforms disponibles  aparecerá de acuerdo con el proceso
  seleccionado antes. Esta opción es adicional, si el usuario necesita publicar campos de Dynaform al final
  del formulario de registro, el usuario elege un Dynaform de esta lista:
  </UL>
  
  @image html ExternalRegistrationFormActionsAfterRegistration.png "Informacion Ater Register"
  
  <b>Actions After Register</b>
  
  <UL type = square>
  <LI> <b>Assign User To:</b> Hay 3 opciones una vez que el usuario ha sido registrado en ProcessMaker:
  <UL>
  
    <LI> <b>Task:</b> Asignar un usuario en una tarea del proceso seleccionado antes.
    
    <LI> <b>Group:</b> Asignar un usuario a un grupo de ProcessMaker.
    
    <LI> <b>Department:</b> Asignar un usuario en un departamento de ProcessMaker.
    
  </UL>

  <LI> <b>Name:</b> Dependiendo de la opción seleccionada en el campo anterior los datos de <b>Name</b> cambian:
  
  @image html 400px-Task-Selected.png "Task Selected"
  
  @image html 400px-Group-Selected.png "Group Selected"
  
  @image html 350px-Department-Selected.png "Department Selected"
  
  <LI> <b>Start a Case on the Task:</b> Cuando se crea un nuevo usuario a este se le asigna un caso que se iniciará
  automáticamente. Elija la tarea del proceso con el que iniciara el usuario. No es necesario elegir las dos opciones
  mencionadas antes.
  </UL>
  
  @section SEC_LIST General List External Registration
  
  @image html ExternalRegistrationMainList.png "Listado General"
  
  donde:
  
  <UL type = square>
  <LI> <b>Title:</b> Titulo del External Registration.

  <LI> <b>Additional Dynaform:</b> Nombre del Dynaform adicional que fue seleccionado.

  <LI> <b>e-mail Template:</b> Nombre de la plantilla que se utiliza para enviar mensajes de correo electrónico.

  <LI> <b>Requests Received:</b> Muestra el número de solicitudes de nuevos usuarios recibidos.

  <LI> <b>Requests Completed:</b> Se muestra el número de solicitudes completadas.
  
  <LI> <b>View ID:</b> Muestra el ID del External Registration Form:
  
  @image html ExternalRegistrationViewID.png "View Id"
  
  <LI> <b>View Form:</b> Muestra el formulario que se publicará, vista previa.
  
  <LI> <b>View Log: </b> Muestra el número de usuarios creados en este External Registration definido antes. Recuerde
  que uno o mas usuarios se pueden crear por formulario de inscripción.
  
  @image html ExternalRegistrationViewLogLink.png "View Log"
  
  <LI> <b>Edit:</b> si algunos cambios son necesarios, haga clic en esta opción para editar el External Registration Form.
  <LI> <b>Delete:</b> Borra el Formulario creado.
  </UL>
  
  @section SEC_CREATE_USER Creating an User Account
  
  Como se mencionó antes, para crear una cuenta de usuario, el enlace debe ser publicado. Además, si el enlace no se
  ha publicado todavía y necesita ser comprobada haga clic en View Form: y se mostrará de la siguiente manera:
  
  @image html ExternalRegistrationCreateUserAccount.png "Creacion de una cuenta de usuario"
  
  Campos a llenar:
  
  <UL type = square>
  <LI> <b>First Name:</b> Nombre de usuario.
  
  <LI> <b>Last Name:</b> Apellido del usuario.
  
  <LI> <b>E-mail:</b> E-mail donde el link sera enviado para activar la cuenta.
  
  <LI> <b>Use E-mail as user name:</b> el campo E-mail puede  ser utilizado como user name, si esta opción esta
  activada, el campo de nombre de usuario se desactivará. Use este correo electrónico para ingresar a ProcessMaker.
  
  <LI> <b>User Name:</b> Escriba un nombre que se utilizará para acceder al sistema en ProcessMaker.
  
  <LI> <b>Password:</b> Introduzca la contraseña que se utiliza para autenticar en ProcessMaker.
  
  <LI> <b>Confirm Password:</b> Vuelva a introducir la contraseña anterior.
  
  <LI> <b>Captcha Code:</b> Escribe los caracteres que se muestran en la imagen. Esto evita software
  automatizado para el llenado del formulario.
  
  </UL>
  
  @image html ExternalRegistrationCreateUserEmail.png "User Email"

  <b>Nota:</b> Si una forma adicional se ha seleccionado, se mostrará a continuación el registro de cuentas de usuario
  como la imagen "Creacion de una cuenta de usuario".
  
  Por último, haga clic en Guardar y continuar para completar el registro, y un mensaje de confirmación aparecerá en la
  pantalla :
  
  @image html ExternalRegistrationCreateUserConfirm.png "Mensaje de Confirmacion"
  
  @section SEC_ACTIVATING_ACCOUNT Activating an Account 
  
  Cuando el registro de usuario se ha completado, se enviará un email al usuario:
  
  @image html ExternalRegistrationCreateUserEmailConf.png
  
  El usuario recibirá toda su información de login en ProcessMaker y la forma de activar la cuenta:
  
  @image html ExternalRegistrationCreateUserEmailTemplte.png "Email Template"
  
  Haga clic en cualquier enlace para activar su cuenta.
  
*/

/**
  @page PAGE_REQUERIMENTS Requeriments
  
  @section SEC_PROCESSMAKER_VERSION ProcessMaker Requirements

  <UL type = square>
  <LI>ProcessMaker V 2.0.37 and later.
  <LI>Configurar Email Notifications.
  </UL>

  @section SEC_PROCESSMAKER_COMPATIBILITY Browser Compatibility

  <UL type = square>
  <LI>Mozilla Firefox from 3.6 and later.
  <LI>Internet Explorer from 7 and later.
  <LI>Chrome .
  </UL>

*/


/**
  @page PAGE_GLOSARY Glosary
*/