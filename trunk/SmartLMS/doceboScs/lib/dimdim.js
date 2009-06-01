/**
**************************************************************************
*                                                                        *
*               DDDDD   iii             DDDDD   iii                      *
*               DD  DD      mm mm mmmm  DD  DD      mm mm mmmm           *
*               DD   DD iii mmm  mm  mm DD   DD iii mmm  mm  mm          *
*               DD   DD iii mmm  mm  mm DD   DD iii mmm  mm  mm          *
*               DDDDDD  iii mmm  mm  mm DDDDDD  iii mmm  mm  mm          *
*                                                                        *
**************************************************************************
**************************************************************************
*                                                                        *
* Part of the DimDim V 1.0 Codebase (http://www.dimdim.com)	             *
*                                                                        *
* Copyright (c) 2006 Communiva Inc. All Rights Reserved.                 *
*                                                                        *
*                                                                        *
* This code is licensed under the DimDim License                         *
* For details please visit http://www.dimdim.com/license/Dimdim-MPL.txt  *
* 									 	                                 *	
* DimDim License is applicable to the following functions:		         *
*                                                                        *
*   function doPost                                                      *
*   function scheduleConference                                          *
*   function startScheduledConference                                    *
*   function startNewConference                                          *
*   function joinConference                                              *
*							                                             *  
*                                                                        *
**************************************************************************  
*/

 /**
  * Post given Query String to the specified URL
  * @public
  * @param {string} url URL
  * @param {string} queryString  Query Sring
  * @param {} successHandler Success Handler
  * @param {} failureHandler Failure Handler
  * @param {} args User-defined argument or arguments to be passed to the callback
  * @return void
 */

function doPost(url,queryString,successHandler,failureHandler,args)
{
	alert(encodeURI(queryString));
	var callToken = new Ajax.Request(
        	url, 
        	{method: 'post', parameters: encodeURI(queryString), onSuccess: successHandler, onFailure: failureHandler }
    );
}

/**
 * All parameters are required unless specified as optional.
 * @public
 * @param {string} baseUrl : is the webapp root url. For Example: http://www.dimdim.com:8080/dimdimfs/
 * @param {string} organizerEmail : this email must be authorized to create conferences on the system.
 * @param {string} organizerDisplayName : the organizer's name.
 * @param {string} conName : name of the conference
 * @param {string} confKey : key that identifies the conference uniquely. Must be between 5 to 7 characters.
 * @param {string} presenters : a ';' seperated list of presenter emails. When the multiple Presenter scenario is implemented this parameter will be come required. Currently this parameter is ignored.
 * @param {string} attendees : a ';' seperated list of attendee emails. When the multiple Presenter scenario is implemented this parameter will be come required. Currently this parameter is ignored.
 * @param {string} timeStr: date and time of the conference. It must be in format:'August 31, 2006 10:30:00 AM'
 * @param {string} timeZone: must be one of the three letter codes (For Example. EST,PST etc) or GMT+/-hh:mm (For Example GMT+5:30)
 * @param {string} sendEmails : must be 'true'/'false'. If true the system will send out invitations to presenters and attendees.
 * @param {function} handleSuccess : Success Handler
 * @param {function} handleFailure : Failure Handler
 * @return {} void
**/

function scheduleConference(baseUrl,organizerEmail,organizerDisplayName,
	confName,confKey,presenters,attendees,timeStr,timeZone,sendEmail,handleSuccess,handleFailure)
{
	var args = ['no','action'];
	var url = baseUrl+"CreateConferenceAPI.action";

	var queryString = "";
	queryString += "organizerEmail=";
	queryString += organizerEmail;
	queryString += "&organizerDisplayName=";
	queryString += organizerDisplayName;
	queryString += "&confName=";
	queryString += confName;
	queryString += "&confKey=";
	queryString += confKey;
	queryString += "&presenters=";
	queryString += presenters;
	queryString += "&attendees=";
	queryString += attendees;
	queryString += "&timeStr=";
	queryString += timeStr;
	queryString += "&timeZone=";
	queryString += timeZone;
	queryString += "&sendEmail=";
	queryString += sendEmail;

	doPost(url,queryString,handleSuccess,handleFailure,args);
}

/**
 * The conference key must be one of the conferences created under the organizer email
 * through the aboce scheduleConference function.
 * @public
 * @param {string} baseUrl Base URL
 * @param {string} email email
 * @param {string} displayName Display Name
 * @param {string} confKey Conference Key
 * @param {function} handleSuccess : Success Handler
 * @param {function} handleFailure : Failure Handler
 * @return void
**/

function startScheduledConference(baseUrl,email,displayName,confKey,handleSuccess,handleFailure)
{
	var args = ['no','action'];
	var url = baseUrl+"StartConferenceCheck.action";

	var queryString = "";
	queryString += "email=";
	queryString += email;
	queryString += "&displayName=";
	queryString += displayName;
	queryString += "&confKey=";
	queryString += confKey;

	doPost(url,queryString,handleSuccess,handleFailure,args);
}

/**
 * Following methods are for unscheduled conferences. The email must be authorized to
 * start a conference.
 * @public
 * @param {string} baseUrl Base URL
 * @param {string} email email
 * @param {string} displayName Display Name
 * @param {string} confKey Conference Key
 * @param {function} handleSuccess : Success Handler
 * @param {function} handleFailure : Failure Handler
 * @return void
**/

function startNewConference(baseUrl,email,displayName,confName,confKey,handleSuccess,handleFailure)
{
	var args = ['no','action'];
	var url = baseUrl+"StartNewConferenceCheck.action";

	var queryString = "";
	queryString += "email=";
	queryString += email;
	queryString += "&displayName=";
	queryString += displayName;
	queryString += "&confName=";
	queryString += confName;
	queryString += "&confKey=";
	queryString += confKey;

	doPost(url,queryString,handleSuccess,handleFailure,args);
}

/**
 * This method allows the user to join a currently running conference.
 * @public
 * @param {string} baseUrl Base URL
 * @param {string} email email
 * @param {string} displayName Display Name
 * @param {string} confKey Conference Key
 * @param {function} handleSuccess : Success Handler
 * @param {function} handleFailure : Failure Handler
 * @return void
**/

function joinConference(baseUrl,email,displayName,confKey,handleSuccess,handleFailure)
{
	var args = ['no','action'];
	var url = baseUrl+"JoinConferenceCheck.action";

	var queryString = "";
	queryString += "email=";
	queryString += email;
	queryString += "&displayName=";
	queryString += displayName;
	queryString += "&confKey=";
	queryString += confKey;

	doPost(url,queryString,handleSuccess,handleFailure,args);
}

