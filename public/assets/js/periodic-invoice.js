$(document).ready(function() {
    // حساب المجموع لكل صف
    function calculateRowTotal() {
        var quantity = parseFloat($(this).closest('tr').find('.quantity').val()) || 0;
        var price = parseFloat($(this).closest('tr').find('.price').val()) || 0;
        var discount = parseFloat($(this).closest('tr').find('.discount-amount').val()) || 0;
        var tax1 = parseFloat($(this).closest('tr').find('.tax1').val()) || 0;
        var tax2 = parseFloat($(this).closest('tr').find('.tax2').val()) || 0;

        var subtotal = quantity * price;
        var total = subtotal - discount;
        var totalWithTax = total + (total * (tax1 / 100)) + (total * (tax2 / 100));

        $(this).closest('tr').find('.row-total').val(totalWithTax.toFixed(2));
        calculateTotals();
    }

    // حساب المجاميع الكلية
    function calculateTotals() {
        var total = 0;
        $('.row-total').each(function() {
            total += parseFloat($(this).val()) || 0;
        });

        $('#total').val(total.toFixed(2));
        
        // حساب الضرائب الإجمالية
        var tax1Total = 0;
        var tax2Total = 0;
        $('.item-row').each(function() {
            var rowTotal = parseFloat($(this).find('.row-total').val()) || 0;
            var tax1 = parseFloat($(this).find('.tax1').val()) || 0;
            var tax2 = parseFloat($(this).find('.tax2').val()) || 0;
            
            tax1Total += rowTotal * (tax1 / 100);
            tax2Total += rowTotal * (tax2 / 100);
        });

        var grandTotal = total + tax1Total + tax2Total;
        $('#grand_total').val(grandTotal.toFixed(2));
        $('#final_amount').val(grandTotal.toFixed(2));
    }

    // تحديث الحسابات عند تغيير أي قيمة
    $(document).on('change', '.quantity, .price, .discount-amount, .tax1, .tax2', calculateRowTotal);
});
