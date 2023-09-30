$(function(){
    const urlParams = new URLSearchParams(window.location.search);
    const refreshParam = urlParams.get('refresh');

    if (refreshParam !== 'true') {
        const newUrl = `${window.location.pathname}?refresh=true`;
        window.location.replace(newUrl);
    }

    var currentItemIndex = 0;
    var $serviceItems = $('.card_service_item');
    var $prevButton = $('#prev-button');
    var $nextButton = $('#next-button');
    var $makeInvoiceButton = $('#MakeInvoice');
    
    function showItem(index) {
        $serviceItems.hide();
        $serviceItems.eq(index).show();
    }
    
    showItem(currentItemIndex);
    updateButtonVisibility();
    
    $nextButton.on('click', function() {
        currentItemIndex++;
        if (currentItemIndex >= $serviceItems.length) {
            currentItemIndex = $serviceItems.length - 1;
        }
        clearInputFields()
        showItem(currentItemIndex);
        updateButtonVisibility();
    });
    
    $prevButton.on('click', function() {
        currentItemIndex--;
        if (currentItemIndex < 0) {
            currentItemIndex = 0;
        }
        clearInputFields()
        showItem(currentItemIndex);
        updateButtonVisibility();
    });

    function updateButtonVisibility() {
        $prevButton.toggle(currentItemIndex > 0);
        $nextButton.toggle(currentItemIndex < $serviceItems.length - 1);
        $makeInvoiceButton.toggle(currentItemIndex === $serviceItems.length - 1);
    }

    
    $('.domaininfo input[type="checkbox"]').change(function() {
        if ($(this).prop('checked')) {
            $('.domaininfo input[type="checkbox"]').not(this).prop('checked', false);
            if ($('.transfer').prop('checked')) {
                
                $('.codetransfer').show();
            } else {
                $('.codetransfer').hide();
            }
        }
    });
    $('.transfer').change(function() {
        if ($(this).prop('checked')) {
            $('.codetransfer').show();
        } else {
            $('.codetransfer').hide();
        }
    });

    $('#btndeleteone').click(function(){
        let key = $(this).val();
        $('.loadajax').load('ajaxuser/delete_one_session_service.php?key='+key)
        location.href.reload();
        location.href.reload();
        return false
    })


    function clearInputFields() {
        $("#titlename").val("");
        $("#domain").val("");
        $("#code").val("");
        $("#colors").val("");
        $("#description").val("");
        $("#filename").val("");
    }
})



