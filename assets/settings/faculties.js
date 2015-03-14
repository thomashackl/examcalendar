$(document).ready(function() {
    $("#resetButton").click(function() {
    	this.form.reset();
    	
        $('input.color').each(function() {
        	this.color.fromString(this.value);
        });

        return false;
    });
});
