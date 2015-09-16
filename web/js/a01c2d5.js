$(function() {
    var totalCount = 0;
    var itemCount  = 0;
    var usedItems  = {};

    Total.total(totalCount);
    Total.subtotal(totalCount);
    Checkout.toggle(itemCount);

    /* item-click */
    $('.js-item-click').on('click', function(e){
        var menuInfo = $(this).attr('id').split('|');
        var item     = menuInfo[0];
        var price    = menuInfo[1];

        if (usedItems[item] != null) {
            usedItems[item] = parseInt(usedItems[item]) + 1;
        } else {
            usedItems[item] = 1;
        }

        var totals = Total.add(item, itemCount, parseInt(usedItems[item]), parseFloat(price), totalCount);
        totalCount = totals[0];
        itemCount  = totals[1];

        Checkout.toggle(itemCount);
    });

    /* remove item */
    $('#register-body').on('click', '.remove-item', function(e) {
        var totals = Total.remove($(this).val(), totalCount, itemCount);
        totalCount = totals[0];
        itemCount  = totals[1];
    });

    /* clear */
    $('.clear-btn').on('click', function(e){
        $('#register-body').empty();

        totalCount = 0;
        Total.total(totalCount);
        Total.subtotal(totalCount);

        $('#js-items-list').empty();
        itemCount = 0;

        Checkout.toggle(itemCount);
    });

    /* change */
    $('#transaction_moneyRecieved').keypress(function(e){
        var regex = new RegExp(/^[0-9.]+/);
        var str   = String.fromCharCode(!e.charCode ? e.which : e.charCode);

        $('#js-paid-error').hide();

        if (regex.test(str)) {
            var paid  = parseFloat($('#transaction_moneyRecieved').val() + parseFloat(str));
            var total = $('#transaction-total').text().substring(1);

            if (paid >= total) {
                var change = paid - total;
                $('#change-total').empty();
                $('#change-total').text('$' + change.toFixed(2));
            } else {
                $('#change-total').empty();
                $('#change-total').text('$--.--');
            }
            return true;
        }

        e.preventDefault();
        /* show error message */
        $('#js-paid-error').show();
        return false;
    });



});

var Total = {
    add: function(item, itemCount, qty, price, totalCount) {

        /* add to form */
        var itemsList = $('#js-items-list');
        var newWidget = itemsList.attr('data-prototype');
        newWidget     = newWidget.replace(/__name__/g, itemCount);
        newWidget     = newWidget.replace(/class="form-control"/g, 'class="form-control" value="' + item + '"')

        var newLi = $('<div id="js-add-item-' + itemCount + '" style="display: none;"></div>').html(newWidget);
        newLi.appendTo($('#js-items-list'));

        /* add to table */
        $('#register-body').append(
            '<tr id="row-item-' + itemCount + '">'
                + '<td>' + item + '</td>'
                + '<td>1</td>'
                + '<td>$' + price.toFixed(2) + '</td>'
                + '<td id="remove-btn-' + itemCount + '">'
                    + '<button class="btn btn-danger remove-item" value="' + itemCount + '|' + price + '" type="button">'
                        + '<i class="fa fa-remove"></i>'
                    + '</button><br>'
                + '</td>'
            + '</tr>'
        );

        /* update total */
        totalCount = totalCount + price;
        Total.total(totalCount);
        Total.subtotal(totalCount);

        return [totalCount, itemCount + 1];
    },

    total: function(totalCount) {
        $('#total-count').empty();
        $('#total-count').text('$' + totalCount.toFixed(2));

        $('.checkout').attr("id", totalCount.toFixed(2));
    },

    subtotal: function(totalCount) {
        $('#sub-total').empty();
        $('#sub-total').text('$' + totalCount.toFixed(2));
    },

    remove: function(itemInfo, totalCount, itemCount) {
        itemInfo  = itemInfo.split('|');
        var id    = itemInfo[0];
        var price = parseFloat(itemInfo[1]);

        /* remove table row */
        $('#row-item-' + id).remove();

        /* remove from form */
        $('#js-add-item-' + id).remove();

        /* update price */
        totalCount = totalCount - price;
        Total.total(totalCount);
        Total.subtotal(totalCount);

        return [totalCount, itemCount - 1];
    }
}

var Checkout = {
    toggle: function(itemCount) {
        if (itemCount == 0) {
            $('.checkout').hide();
        } else {
            $('.checkout').show();
        }
    }
}

