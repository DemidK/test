<?php

// Define the PHP array representing the XML structure
$invoiceArray = [
    'CustomizationID' => 'urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0',
    'ProfileID' => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
    'ID' => '012458-4567',
    'IssueDate' => '2025-02-12',
    'DueDate' => '2025-02-26',
    'InvoiceTypeCode' => '380',
    'Note' => '-',
    'DocumentCurrencyCode' => 'EUR',
    'BuyerReference' => '40003893156',
    'ContractDocumentReference' => [
        'ID' => 'RD-1234567',
    ],
    'AccountingSupplierParty' => [
        'Party' => [
            'EndpointID' => [
                '_attributes' => ['schemeID' => '9939'],
                '_value' => '40103309759',
            ],
            'PartyIdentification' => [
                'ID' => '40103309759',
            ],
            'PartyName' => [
                'Name' => 'SIA "RD Consult"',
            ],
            'PostalAddress' => [
                'AddressLine' => [
                    'Line' => 'Dzelzavas 38-5',
                ],
                'Country' => [
                    'IdentificationCode' => 'LV',
                ],
            ],
            'PartyTaxScheme' => [
                'RegistrationName' => 'SIA "RD Consult"',
                'CompanyID' => 'LV 40103309759',
                'TaxScheme' => [
                    'ID' => 'VAT',
                ],
            ],
            'PartyLegalEntity' => [
                'RegistrationName' => 'SIA "RD Consult"',
                'CompanyID' => 'LV40103309759',
            ],
        ],
    ],
    'AccountingCustomerParty' => [
        'Party' => [
            'EndpointID' => [
                '_attributes' => ['schemeID' => '9939'],
                '_value' => '40003893156',
            ],
            'PartyIdentification' => [
                'ID' => '40003893156',
            ],
            'PartyName' => [
                'Name' => 'SIA „Vājstrāvas Tīkli”',
            ],
            'PostalAddress' => [
                'AddressLine' => [
                    'Line' => 'Dzelzavas iela 117, Rīga, LV-1082',
                ],
                'Country' => [
                    'IdentificationCode' => 'LV',
                ],
            ],
            'PartyTaxScheme' => [
                'RegistrationName' => 'SIA „Vājstrāvas Tīkli”',
                'CompanyID' => 'LV40003893156',
                'TaxScheme' => [
                    'ID' => 'VAT',
                ],
            ],
            'PartyLegalEntity' => [
                'RegistrationName' => 'SIA „Vājstrāvas Tīkli”',
                'CompanyID' => 'LV40003893156',
            ],
        ],
    ],
    'TaxTotal' => [
        'TaxAmount' => [
            '_attributes' => ['currencyID' => 'EUR'],
            '_value' => '42.00',
        ],
        'TaxSubtotal' => [
            'TaxableAmount' => [
                '_attributes' => ['currencyID' => 'EUR'],
                '_value' => '200.00',
            ],
            'TaxAmount' => [
                '_attributes' => ['currencyID' => 'EUR'],
                '_value' => '42.00',
            ],
            'TaxCategory' => [
                'ID' => 'S',
                'Name' => 'VAT 21%',
                'Percent' => '21',
                'TaxScheme' => [
                    'ID' => 'VAT',
                ],
            ],
        ],
    ],
    'LegalMonetaryTotal' => [
        'LineExtensionAmount' => [
            '_attributes' => ['currencyID' => 'EUR'],
            '_value' => '200.00',
        ],
        'TaxExclusiveAmount' => [
            '_attributes' => ['currencyID' => 'EUR'],
            '_value' => '200.00',
        ],
        'TaxInclusiveAmount' => [
            '_attributes' => ['currencyID' => 'EUR'],
            '_value' => '242.00',
        ],
        'PrepaidAmount' => [
            '_attributes' => ['currencyID' => 'EUR'],
            '_value' => '0.00',
        ],
        'PayableAmount' => [
            '_attributes' => ['currencyID' => 'EUR'],
            '_value' => '242.00',
        ],
    ],
    'InvoiceLine' => [
        [
            'ID' => '1',
            'InvoicedQuantity' => [
                '_attributes' => ['unitCode' => 'H87'],
                '_value' => '1.00',
            ],
            'LineExtensionAmount' => [
                '_attributes' => ['currencyID' => 'EUR'],
                '_value' => '20.00',
            ],
            'AllowanceCharge' => [
                'ChargeIndicator' => 'false',
                'AllowanceChargeReason' => '-',
                'Amount' => [
                    '_attributes' => ['currencyID' => 'EUR'],
                    '_value' => '0.00',
                ],
            ],
            'Item' => [
                'Name' => 'Internets',
                'ClassifiedTaxCategory' => [
                    'ID' => 'S',
                    'Percent' => '21',
                    'TaxScheme' => [
                        'ID' => 'VAT',
                    ],
                ],
            ],
            'Price' => [
                'PriceAmount' => [
                    '_attributes' => ['currencyID' => 'EUR'],
                    '_value' => '20.0000',
                ],
                'BaseQuantity' => '1.00',
            ],
        ],
        [
            'ID' => '2',
            'InvoicedQuantity' => [
                '_attributes' => ['unitCode' => 'H87'],
                '_value' => '1.00',
            ],
            'LineExtensionAmount' => [
                '_attributes' => ['currencyID' => 'EUR'],
                '_value' => '180.00',
            ],
            'AllowanceCharge' => [
                'ChargeIndicator' => 'false',
                'AllowanceChargeReason' => '-',
                'Amount' => [
                    '_attributes' => ['currencyID' => 'EUR'],
                    '_value' => '0.00',
                ],
            ],
            'Item' => [
                'Name' => 'Montažas darbi',
                'ClassifiedTaxCategory' => [
                    'ID' => 'S',
                    'Percent' => '21',
                    'TaxScheme' => [
                        'ID' => 'VAT',
                    ],
                ],
            ],
            'Price' => [
                'PriceAmount' => [
                    '_attributes' => ['currencyID' => 'EUR'],
                    '_value' => '180.0000',
                ],
                'BaseQuantity' => '1.00',
            ],
        ],
    ],
];

// Function to recursively add array data to XML
function arrayToXml($array, &$xml) {
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            if (isset($value['_attributes'])) {
                $subnode = $xml->addChild($key, $value['_value']);
                foreach ($value['_attributes'] as $attrKey => $attrValue) {
                    $subnode->addAttribute($attrKey, $attrValue);
                }
            } else {
                $subnode = $xml->addChild($key);
                arrayToXml($value, $subnode);
            }
        } else {
            $xml->addChild($key, htmlspecialchars($value));
        }
    }
}

// Create the root element with namespaces
$xml = new SimpleXMLElement('<Invoice/>');
$xml->addAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
$xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$xml->addAttribute('xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
$xml->addAttribute('xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
$xml->addAttribute('xmlns', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2');

// Convert the array to XML
arrayToXml($invoiceArray, $xml);

// Output the XML
echo $xml->asXML();

?>


//-------------------------------------2---------------------------$_COOKIE

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Form</title>
    <style>
        .invoice-line {
            margin-bottom: 10px;
            border: 1px solid #ccc;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>Invoice Form</h1>
    <form action="generate_xml.php" method="POST">
        <!-- General Invoice Information -->
        <fieldset>
            <legend>Invoice Details</legend>
            <label for="CustomizationID">Customization ID:</label>
            <input type="text" id="CustomizationID" name="CustomizationID" required><br>

            <label for="ProfileID">Profile ID:</label>
            <input type="text" id="ProfileID" name="ProfileID" required><br>

            <label for="ID">Invoice ID:</label>
            <input type="text" id="ID" name="ID" required><br>

            <label for="IssueDate">Issue Date:</label>
            <input type="date" id="IssueDate" name="IssueDate" required><br>

            <label for="DueDate">Due Date:</label>
            <input type="date" id="DueDate" name="DueDate" required><br>

            <label for="InvoiceTypeCode">Invoice Type Code:</label>
            <input type="text" id="InvoiceTypeCode" name="InvoiceTypeCode" required><br>

            <label for="Note">Note:</label>
            <input type="text" id="Note" name="Note" required><br>

            <label for="DocumentCurrencyCode">Document Currency Code:</label>
            <input type="text" id="DocumentCurrencyCode" name="DocumentCurrencyCode" required><br>

            <label for="BuyerReference">Buyer Reference:</label>
            <input type="text" id="BuyerReference" name="BuyerReference" required><br>
        </fieldset>

        <!-- Supplier Information -->
        <fieldset>
            <legend>Supplier Details</legend>
            <label for="SupplierEndpointID">Supplier Endpoint ID:</label>
            <input type="text" id="SupplierEndpointID" name="SupplierEndpointID" required><br>

            <label for="SupplierID">Supplier ID:</label>
            <input type="text" id="SupplierID" name="SupplierID" required><br>

            <label for="SupplierName">Supplier Name:</label>
            <input type="text" id="SupplierName" name="SupplierName" required><br>

            <label for="SupplierAddress">Supplier Address:</label>
            <input type="text" id="SupplierAddress" name="SupplierAddress" required><br>

            <label for="SupplierCountryCode">Supplier Country Code:</label>
            <input type="text" id="SupplierCountryCode" name="SupplierCountryCode" required><br>

            <label for="SupplierTaxName">Supplier Tax Name:</label>
            <input type="text" id="SupplierTaxName" name="SupplierTaxName" required><br>

            <label for="SupplierCompanyID">Supplier Company ID:</label>
            <input type="text" id="SupplierCompanyID" name="SupplierCompanyID" required><br>
        </fieldset>

        <!-- Customer Information -->
        <fieldset>
            <legend>Customer Details</legend>
            <label for="CustomerEndpointID">Customer Endpoint ID:</label>
            <input type="text" id="CustomerEndpointID" name="CustomerEndpointID" required><br>

            <label for="CustomerID">Customer ID:</label>
            <input type="text" id="CustomerID" name="CustomerID" required><br>

            <label for="CustomerName">Customer Name:</label>
            <input type="text" id="CustomerName" name="CustomerName" required><br>

            <label for="CustomerAddress">Customer Address:</label>
            <input type="text" id="CustomerAddress" name="CustomerAddress" required><br>

            <label for="CustomerCountryCode">Customer Country Code:</label>
            <input type="text" id="CustomerCountryCode" name="CustomerCountryCode" required><br>

            <label for="CustomerTaxName">Customer Tax Name:</label>
            <input type="text" id="CustomerTaxName" name="CustomerTaxName" required><br>

            <label for="CustomerCompanyID">Customer Company ID:</label>
            <input type="text" id="CustomerCompanyID" name="CustomerCompanyID" required><br>
        </fieldset>

        <!-- Tax and Monetary Information -->
        <fieldset>
            <legend>Tax and Monetary Details</legend>
            <label for="TaxAmount">Tax Amount:</label>
            <input type="number" step="0.01" id="TaxAmount" name="TaxAmount" required><br>

            <label for="TaxableAmount">Taxable Amount:</label>
            <input type="number" step="0.01" id="TaxableAmount" name="TaxableAmount" required><br>

            <label for="TaxPercent">Tax Percent:</label>
            <input type="number" step="0.01" id="TaxPercent" name="TaxPercent" required><br>

            <label for="LineExtensionAmount">Line Extension Amount:</label>
            <input type="number" step="0.01" id="LineExtensionAmount" name="LineExtensionAmount" required><br>

            <label for="PayableAmount">Payable Amount:</label>
            <input type="number" step="0.01" id="PayableAmount" name="PayableAmount" required><br>
        </fieldset>

        <!-- Invoice Line Items -->
        <fieldset>
            <legend>Invoice Line Items</legend>
            <div id="invoice-lines">
                <div class="invoice-line">
                    <label for="ItemName">Item Name:</label>
                    <input type="text" name="ItemName[]" required><br>

                    <label for="ItemQuantity">Item Quantity:</label>
                    <input type="number" step="0.01" name="ItemQuantity[]" required><br>

                    <label for="ItemPrice">Item Price:</label>
                    <input type="number" step="0.01" name="ItemPrice[]" required><br>
                </div>
            </div>
            <button type="button" id="add-invoice-line">Add Invoice Line</button>
        </fieldset>

        <button type="submit">Generate XML</button>
    </form>

    <script>
        // JavaScript to add new invoice lines
        document.getElementById('add-invoice-line').addEventListener('click', function () {
            const invoiceLines = document.getElementById('invoice-lines');
            const newLine = document.createElement('div');
            newLine.classList.add('invoice-line');
            newLine.innerHTML = `
                <label for="ItemName">Item Name:</label>
                <input type="text" name="ItemName[]" required><br>

                <label for="ItemQuantity">Item Quantity:</label>
                <input type="number" step="0.01" name="ItemQuantity[]" required><br>

                <label for="ItemPrice">Item Price:</label>
                <input type="number" step="0.01" name="ItemPrice[]" required><br>
            `;
            invoiceLines.appendChild(newLine);
        });
    </script>
</body>
</html>


<?php
// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $formData = [
        'CustomizationID' => $_POST['CustomizationID'],
        'ProfileID' => $_POST['ProfileID'],
        'ID' => $_POST['ID'],
        'IssueDate' => $_POST['IssueDate'],
        'DueDate' => $_POST['DueDate'],
        'InvoiceTypeCode' => $_POST['InvoiceTypeCode'],
        'Note' => $_POST['Note'],
        'DocumentCurrencyCode' => $_POST['DocumentCurrencyCode'],
        'BuyerReference' => $_POST['BuyerReference'],
        'SupplierEndpointID' => $_POST['SupplierEndpointID'],
        'SupplierID' => $_POST['SupplierID'],
        'SupplierName' => $_POST['SupplierName'],
        'SupplierAddress' => $_POST['SupplierAddress'],
        'SupplierCountryCode' => $_POST['SupplierCountryCode'],
        'SupplierTaxName' => $_POST['SupplierTaxName'],
        'SupplierCompanyID' => $_POST['SupplierCompanyID'],
        'CustomerEndpointID' => $_POST['CustomerEndpointID'],
        'CustomerID' => $_POST['CustomerID'],
        'CustomerName' => $_POST['CustomerName'],
        'CustomerAddress' => $_POST['CustomerAddress'],
        'CustomerCountryCode' => $_POST['CustomerCountryCode'],
        'CustomerTaxName' => $_POST['CustomerTaxName'],
        'CustomerCompanyID' => $_POST['CustomerCompanyID'],
        'TaxAmount' => $_POST['TaxAmount'],
        'TaxableAmount' => $_POST['TaxableAmount'],
        'TaxPercent' => $_POST['TaxPercent'],
        'LineExtensionAmount' => $_POST['LineExtensionAmount'],
        'PayableAmount' => $_POST['PayableAmount'],
        'ItemNames' => $_POST['ItemName'],
        'ItemQuantities' => $_POST['ItemQuantity'],
        'ItemPrices' => $_POST['ItemPrice'],
    ];

    // Create the XML structure
    $xml = new SimpleXMLElement('<Invoice/>');
    $xml->addAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
    $xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $xml->addAttribute('xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
    $xml->addAttribute('xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
    $xml->addAttribute('xmlns', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2');

    // Add invoice details
    $xml->addChild('cbc:CustomizationID', $formData['CustomizationID']);
    $xml->addChild('cbc:ProfileID', $formData['ProfileID']);
    $xml->addChild('cbc:ID', $formData['ID']);
    $xml->addChild('cbc:IssueDate', $formData['IssueDate']);
    $xml->addChild('cbc:DueDate', $formData['DueDate']);
    $xml->addChild('cbc:InvoiceTypeCode', $formData['InvoiceTypeCode']);
    $xml->addChild('cbc:Note', $formData['Note']);
    $xml->addChild('cbc:DocumentCurrencyCode', $formData['DocumentCurrencyCode']);
    $xml->addChild('cbc:BuyerReference', $formData['BuyerReference']);

    // Add supplier details
    $supplierParty = $xml->addChild('cac:AccountingSupplierParty')->addChild('cac:Party');
    $supplierParty->addChild('cbc:EndpointID', $formData['SupplierEndpointID'])->addAttribute('schemeID', '9939');
    $supplierParty->addChild('cac:PartyIdentification')->addChild('cbc:ID', $formData['SupplierID']);
    $supplierParty->addChild('cac:PartyName')->addChild('cbc:Name', $formData['SupplierName']);
    $postalAddress = $supplierParty->addChild('cac:PostalAddress');
    $postalAddress->addChild('cac:AddressLine')->addChild('cbc:Line', $formData['SupplierAddress']);
    $postalAddress->addChild('cac:Country')->addChild('cbc:IdentificationCode', $formData['SupplierCountryCode']);
    $supplierParty->addChild('cac:PartyTaxScheme')->addChild('cbc:RegistrationName', $formData['SupplierTaxName']);
    $supplierParty->addChild('cac:PartyLegalEntity')->addChild('cbc:CompanyID', $formData['SupplierCompanyID']);

    // Add customer details
    $customerParty = $xml->addChild('cac:AccountingCustomerParty')->addChild('cac:Party');
    $customerParty->addChild('cbc:EndpointID', $formData['CustomerEndpointID'])->addAttribute('schemeID', '9939');
    $customerParty->addChild('cac:PartyIdentification')->addChild('cbc:ID', $formData['CustomerID']);
    $customerParty->addChild('cac:PartyName')->addChild('cbc:Name', $formData['CustomerName']);
    $postalAddress = $customerParty->addChild('cac:PostalAddress');
    $postalAddress->addChild('cac:AddressLine')->addChild('cbc:Line', $formData['CustomerAddress']);
    $postalAddress->addChild('cac:Country')->addChild('cbc:IdentificationCode', $formData['CustomerCountryCode']);
    $customerParty->addChild('cac:PartyTaxScheme')->addChild('cbc:RegistrationName', $formData['CustomerTaxName']);
    $customerParty->addChild('cac:PartyLegalEntity')->addChild('cbc:CompanyID', $formData['CustomerCompanyID']);

    // Add tax and monetary details
    $taxTotal = $xml->addChild('cac:TaxTotal');
    $taxTotal->addChild('cbc:TaxAmount', $formData['TaxAmount'])->addAttribute('currencyID', 'EUR');
    $taxSubtotal = $taxTotal->addChild('cac:TaxSubtotal');
    $taxSubtotal->addChild('cbc:TaxableAmount', $formData['TaxableAmount'])->addAttribute('currencyID', 'EUR');
    $taxSubtotal->addChild('cbc:TaxAmount', $formData['TaxAmount'])->addAttribute('currencyID', 'EUR');
    $taxCategory = $taxSubtotal->addChild('cac:TaxCategory');
    $taxCategory->addChild('cbc:ID', 'S');
    $taxCategory->addChild('cbc:Name', 'VAT ' . $formData['TaxPercent'] . '%');
    $taxCategory->addChild('cbc:Percent', $formData['TaxPercent']);
    $taxCategory->addChild('cac:TaxScheme')->addChild('cbc:ID', 'VAT');

    $legalMonetaryTotal = $xml->addChild('cac:LegalMonetaryTotal');
    $legalMonetaryTotal->addChild('cbc:LineExtensionAmount', $formData['LineExtensionAmount'])->addAttribute('currencyID', 'EUR');
    $legalMonetaryTotal->addChild('cbc:PayableAmount', $formData['PayableAmount'])->addAttribute('currencyID', 'EUR');

    // Add invoice line items
    foreach ($formData['ItemNames'] as $index => $itemName) {
        $invoiceLine = $xml->addChild('cac:InvoiceLine');
        $invoiceLine->addChild('cbc:ID', $index + 1);
        $invoiceLine->addChild('cbc:InvoicedQuantity', $formData['ItemQuantities'][$index])->addAttribute('unitCode', 'H87');
        $invoiceLine->addChild('cbc:LineExtensionAmount', $formData['ItemPrices'][$index])->addAttribute('currencyID', 'EUR');
        $item = $invoiceLine->addChild('cac:Item');
        $item->addChild('cbc:Name', $itemName);
        $item->addChild('cac:ClassifiedTaxCategory')->addChild('cbc:ID', 'S');
    }

    // Output the XML
    header('Content-Type: application/xml');
    echo $xml->asXML();
} else {
    echo "Form data not submitted.";
}
?>