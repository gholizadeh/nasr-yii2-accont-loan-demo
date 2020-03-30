//$.cookie.json = true;
$.cookie.defaults.path = '/';

function IsValidJSONString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function is_json( _obj )
{
    var _has_keys = 0 ;
    for( var _pr in _obj ){
        if ( _obj.hasOwnProperty( _pr ) && !( /^\d+$/.test( _pr ) ) ){
           _has_keys = 1 ;
           break ;
        }
    }

    return ( _has_keys && _obj.constructor == Object && _obj.constructor != Array ) ? 1 : 0 ;
}

function getStorage(name) {
    if (typeof(Storage) !== "undefined") {
        //epoch time, lets deal only with integer
        var now = Date.now();
        // set expiration for storage
        var expiresIn = localStorage.getItem(name+'_expiresIn');
        if (expiresIn===undefined || expiresIn===null) { expiresIn = 0; }

        if (expiresIn < now) {// Expired
            removeStorage(name);
            return null;
        } else {
            try {
                var value = localStorage.getItem(name);
                if(IsValidJSONString(value))
                	return JSON.parse(value);
                return value;
            } catch(e) {
                console.log('getStorage: Error reading key ['+ name + '] from localStorage: ' + JSON.stringify(e) );
                return null;
            }
        }
    } else {
        //we should use cookie 
        if (!$.cookie(name))
        	return null;

        value = $.cookie(name);
        if(IsValidJSONString(value))
        	return JSON.parse(value);
                
        return value;
    }
}

function setStorage(name, value, exdays) {

	if(is_json(value))
		value = JSON.stringify(value);

    if (exdays===undefined || exdays===null) {
        exdays = 1;  // default: 1 day
    } else {
        exdays = Math.abs(exdays); //make sure it's positive
    }
    //millisecs since epoch time, lets deal only with integer
    var now = Date.now();  
    var schedule = now + (exdays * 24 * 60 * 60 * 1000); 

    if (typeof(Storage) !== "undefined") {

        try {
            localStorage.setItem(name, value);
            localStorage.setItem(name + '_expiresIn', schedule);
        } catch(e) {
            console.log('setStorage: Error setting key ['+ name + '] in localStorage: ' + JSON.stringify(e) );
            return false;
        }

    } else {
        var d = new Date();
        d.setTime(schedule);
        $.cookie(name, value, {expires: d});
    }

    return true;
}

function removeStorage(name) {
    if (typeof(Storage) !== "undefined") {
        try {
            localStorage.removeItem(name);
            localStorage.removeItem(name + '_expiresIn');
        } catch(e) {
            console.log('removeStorage: Error removing key ['+ name + '] from localStorage: ' + JSON.stringify(e) );
            return false;
        }
        
    } else {
    	$.removeCookie(name);
    }

    return true;
};