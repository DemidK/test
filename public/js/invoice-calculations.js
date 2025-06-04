/******/ (() => { // webpackBootstrap
/*!**********************************************!*\
  !*** ./resources/js/invoice-calculations.js ***!
  \**********************************************/
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
document.addEventListener('DOMContentLoaded', function () {
  var container = document.getElementById('items-container');
  var addButton = document.getElementById('add-item');

  // Handle numeric input to prevent leading zeros
  function handleNumericInput(input) {
    var value = input.value;

    // Handle decimal numbers
    if (value.includes('.')) {
      var _value$split = value.split('.'),
        _value$split2 = _slicedToArray(_value$split, 2),
        whole = _value$split2[0],
        decimal = _value$split2[1];
      // Remove leading zeros from whole part
      if (whole.length > 1 && whole.startsWith('0')) {
        whole = parseFloat(whole) || '0';
      }
      value = whole + '.' + decimal;
    }
    // Handle whole numbers
    else if (value.length > 1 && value.startsWith('0')) {
      value = parseFloat(value) || '0';
    }

    // Update input value
    input.value = value;
    return value;
  }

  // Calculate totals for a single item
  function calculateItemTotal(item) {
    var qty = parseFloat(item.querySelector('.item-qty').value) || 0;
    var price = parseFloat(item.querySelector('.item-price').value) || 0;
    var vat = parseFloat(item.querySelector('.item-vat').value) || 0;
    var subtotal = qty * price;
    var vatAmount = vat > 0 ? subtotal * vat / 100 : 0;
    var total = subtotal + vatAmount;

    // Update total display
    item.querySelector('.item-total').textContent = '$' + formatCurrency(total);
    return {
      subtotal: subtotal,
      vatAmount: vatAmount,
      total: total
    };
  }

  // Format currency
  function formatCurrency(number) {
    return new Intl.NumberFormat('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(number);
  }

  // Update all totals
  function updateTotals() {
    var grandTotal = 0;
    var totalVat = 0;
    var subtotal = 0;
    container.querySelectorAll('.item').forEach(function (item) {
      var itemTotals = calculateItemTotal(item);
      grandTotal += itemTotals.total;
      totalVat += itemTotals.vatAmount;
      subtotal += itemTotals.subtotal;
    });

    // Update display totals
    document.getElementById('total-display').textContent = formatCurrency(grandTotal);
    document.getElementById('total-vat-display').textContent = formatCurrency(totalVat);
    document.getElementById('total-wo-vat-display').textContent = formatCurrency(subtotal);

    // Update hidden inputs
    document.getElementById('total_amount').value = grandTotal.toFixed(2);
    document.getElementById('total_vat').value = totalVat.toFixed(2);
    document.getElementById('total_wo_vat').value = subtotal.toFixed(2);
  }

  // Add event listeners to an item
  function addItemListeners(item) {
    // Input change listeners
    item.querySelectorAll('input').forEach(function (input) {
      if (input.type === 'number') {
        // Handle input event for numeric fields
        input.addEventListener('input', function () {
          handleNumericInput(input);
          updateTotals();
        });

        // Handle blur event for numeric fields
        input.addEventListener('blur', function () {
          if (input.value === '') {
            if (input.classList.contains('item-qty')) {
              input.value = '1';
            } else {
              input.value = '0';
            }
          }
          // Format decimal numbers
          if (input.classList.contains('item-price')) {
            input.value = parseFloat(input.value).toFixed(2);
          }
          updateTotals();
        });
      }
    });

    // Remove item button
    var removeButton = item.querySelector('.remove-item');
    if (removeButton) {
      removeButton.addEventListener('click', function () {
        if (container.querySelectorAll('.item').length > 1) {
          item.remove();
          updateTotals();
        } else {
          alert('At least one item is required.');
        }
      });
    }
  }

  // Add new item
  addButton === null || addButton === void 0 || addButton.addEventListener('click', function () {
    var itemCount = container.querySelectorAll('.item').length;
    var newItem = document.createElement('div');
    newItem.className = 'item grid grid-cols-6 gap-4 mb-2 p-3 bg-gray-50 rounded-lg';
    newItem.innerHTML = "\n            <div class=\"col-span-2\">\n                <input type=\"text\" \n                    name=\"items[".concat(itemCount, "][description]\" \n                    placeholder=\"Apraksts\" \n                    class=\"w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-200\" \n                    required>\n            </div>\n            <div>\n                <input type=\"number\" \n                    name=\"items[").concat(itemCount, "][quantity]\" \n                    placeholder=\"Qty\" \n                    min=\"1\" \n                    class=\"w-full px-3 py-2 border rounded-lg text-center item-qty focus:ring-2 focus:ring-blue-200\" \n                    value=\"1\" \n                    required>\n            </div>\n            <div>\n                <div class=\"relative\">\n                    <span class=\"absolute left-3 top-1/2 -translate-y-1/2 text-gray-500\">$</span>\n                    <input type=\"number\" \n                        name=\"items[").concat(itemCount, "][price]\" \n                        placeholder=\"0.00\" \n                        step=\"0.01\" \n                        min=\"0\" \n                        class=\"w-full px-3 py-2 pl-7 border rounded-lg text-right item-price focus:ring-2 focus:ring-blue-200\" \n                        value=\"0\" \n                        required>\n                </div>\n            </div>\n            <div>\n                <div class=\"relative\">\n                    <input type=\"number\" \n                        name=\"items[").concat(itemCount, "][vat]\" \n                        placeholder=\"0\" \n                        min=\"0\" \n                        max=\"100\" \n                        class=\"w-full px-3 py-2 pr-7 border rounded-lg text-right item-vat focus:ring-2 focus:ring-blue-200\" \n                        value=\"0\" \n                        required>\n                    <span class=\"absolute right-3 top-1/2 -translate-y-1/2 text-gray-500\">%</span>\n                </div>\n            </div>\n            <div class=\"flex items-center justify-between\">\n                <span class=\"item-total font-medium\">$0.00</span>\n                <button type=\"button\" class=\"remove-item text-red-500 hover:text-red-700\">\n                    <svg class=\"w-5 h-5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" \n                            d=\"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\"/>\n                    </svg>\n                </button>\n            </div>\n        ");
    container.appendChild(newItem);
    addItemListeners(newItem);
    updateTotals();

    // Focus on the description field of the new item
    newItem.querySelector('input[type="text"]').focus();
  });

  // Initialize listeners for existing items
  container.querySelectorAll('.item').forEach(addItemListeners);
  updateTotals();
});
/******/ })()
;