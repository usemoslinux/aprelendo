<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</head>
<body>
    <div></div>
    <script>

    $(document).ready(function () {
        var xml = '<transcript><text start="1.101" dur="2.035">The immigration debate continues to dominate the news.</text><text start="3.169" dur="2.803">Families separated, children in cages,</text><text start="6.005" dur="3.103">and now people are going hungry.</text><text start="9.141" dur="1.903">A Virginia restaurant refused service</text><text start="11.077" dur="2.636">to White House Press Secretary Sarah Sanders.</text><text start="13.746" dur="2.369">Stephanie Wilkinson, owner of the Red Hen</text></transcript>';
        var $xml_obj = $.parseXML(xml);

        var $first_txt_obj = $($xml_obj).find('text:first');
        var cur_start = $first_txt_obj.attr('start');
        var cur_dur = $first_txt_obj.attr('dur');

        // setTimeout(loop(cur_start), cur_start * 10000);  

        setTimeout(() => {
            loop(cur_start);
        }, cur_start * 1000);

        function loop(start) {
            var $text_obj = $($xml_obj).find("text[start='" + start + "']");
            
            if ($text_obj.length > 0) {
                var new_start = $text_obj.next('text').attr('start');
                var new_dur = $text_obj.next('text').attr('dur') * 1000;

                $('div').text(($text_obj.text()));
                
                setTimeout(() => {
                    loop(new_start);
                }, new_dur);
                
            }
        }
    });
    </script>
</body>
</html>