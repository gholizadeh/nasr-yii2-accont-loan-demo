function translateToEng( string ){
    var pn = ["۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹"];
    var en = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
    for (var i = 0; i < 10; i++) {
        var regex_fa = new RegExp(pn[i], 'g');
        string = string.replace(regex_fa, en[i]);
    }
    return string;
}

function convertDigitsToWords( numbers ) {
  var result;
  if (numbers.toString().length > 0) {
      result = NumToPersian(numbers) + " تومان";
  }
  $('#result').html(result);
  $('#number-result').val(numbers);
}

$('input.num').on('keyup', function (event) {
  this.value = translateToEng(this.value.split(',').join('')).replace(/\D/g,'');
});

$('input.number').on('keyup', function (event) {
  $value = translateToEng(this.value.split(',').join('')).replace(/\D/g,'');
  this.value = $value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  if ( this.value.length > 0 ){
    $(this).removeClass('is-invalid');
  } else {
    $(this).addClass('is-invalid');
  }
  convertDigitsToWords($value);
});

$(function() { $('input.number').keyup(); });