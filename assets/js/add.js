    $(document).ready(function(){
       var DATE_FORMAT={dateFormat: 'dd/mm/yy'};
        $("#ReceivedDate").datepicker(DATE_FORMAT);
        var currentDate = new Date();  
        $("#ReceivedDate").datepicker("setDate",currentDate);
        $("#btn_reset").on("click",function(){
            if($("#suppliercheckbox").prop('checked')==false)
            {
                $(this).closest('form').find("#SupplierName,#Model, #SerialNumber,#Note,#EbayItemNo").val("");       
            }
            else
            {
                $(this).closest('form').find("#Model, #SerialNumber,#Note,#EbayItemNo").val("");     
            }
        });
    });