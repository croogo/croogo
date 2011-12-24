/*
Usage 1: define the default prefix by using an object with the property prefix as a parameter which contains a string value; {prefix: 'id'}
Usage 2: call the function jQuery.uuid() with a string parameter p to be used as a prefix to generate a random uuid;
Usage 3: call the function jQuery.uuid() with no parameters to generate a uuid with the default prefix; defaul prefix: '' (empty string)
*/

/*
Generates random uuid
*/
jQuery.fn.uuid = function (p) {

    /*
     *Generate fragment of random numbers
     */
    var uuid_default_prefix = '';
    var uuidlet = function () {
	    return(((1+Math.random())*0x10000)|0).toString(16).substring(1);
    };

	if (typeof(p) == 'object' && typeof(p.prefix) == 'string') {
		uuid_default_prefix = p.prefix;
	} else {
		p = p || jQuery._uuid_default_prefix || '';
		return(p + uuidlet() + uuidlet() + "-" + uuidlet() + "-" + uuidlet() +
            "-" + uuidlet() + "-" + uuidlet() + uuidlet() + uuidlet());
	};
};