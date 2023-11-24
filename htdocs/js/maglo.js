$(function(){
    $('#dtp, #dtp2').datetimepicker();
});


$(function() {
    var target;
    var local;

    jQuery('.confirm').click( function() {
        target = $(this).closest('form');
        local = this;


        if ($(this).attr('name') == 'add') {
            $('#dialogmsg').text("追加しますか？");
        } else if ($(this).attr('name') == 'modify') {
            $('#dialogmsg').text("更新しますか？");
        } else if ($(this).attr('name') == 'delete') {
            $('#dialogmsg').text("削除しますか？");
        } else if ($(this).attr('name') == 'break') {
            $('#dialogmsg').text("配信を中断しますか？");
        } else if ($(this).attr('name') == 'restart') {
            $('#dialogmsg').text("配信を再開しますか？");
        } 
 

        jQuery('#jquery-ui-dialog').dialog('open');
    } );

    jQuery('#jquery-ui-dialog').dialog( {
        autoOpen: false,
        show: 'drop',
        hide: {
          effect: 'explode',
          duration: 200
        },
        modal: true,
        buttons: {
            'OK': function() {
                jQuery(this).dialog('close');
                $('<input>').attr('type','hidden')
                    .attr('name',$(local).attr('name'))
                    .appendTo(target);
                $(target).submit();
            },
            'キャンセル': function() {
                jQuery(this).dialog('close');
            },
        }
    } );
});
