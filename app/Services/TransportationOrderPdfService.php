<?php

namespace App\Services;

use App\Models\TransportationOrder;
use TCPDF;
use Carbon\Carbon;

class TransportationOrderPdfService
{
    public function generate(TransportationOrder $order)
    {
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('SV Pluss, SIA');
        $pdf->SetAuthor('Transportation Management System');
        $pdf->SetTitle('Pasūtījums / Заказ nr. ' . $order->id);

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('dejavusans', '', 10);

        // Generate PDF content
        $this->addOrderHeader($pdf, $order);
        $this->addOrderDetails($pdf, $order);
        $this->addAdditionalInformation($pdf, $order);
        $this->addPartyInformation($pdf, $order);

        // Close and output PDF document
        return $pdf->Output('transportation_order_' . $order->id . '.pdf', 'I');
    }

    private function addOrderHeader(TCPDF $pdf, $order)
    {
        $pdf->SetFont('dejavusans', 'B', 14);
        $pdf->Cell(0, 10, 'Pasūtījums / Заказ nr. ' . $order->id, 0, 1, 'C');
    }

    private function addOrderDetails(TCPDF $pdf, $order)
    {
        $pdf->SetFont('dejavusans', '', 10);
        
        // Table with load/unload details
        $html = '<table border="1" cellpadding="4">
            <tr>
                <td width="50%"><strong>Iekraušanas adreses / Адреса загрузок:</strong><br>' . 
                    htmlspecialchars($order->load_address ?? 'Nav norādīts') . '<br>' .
                    'Iekraušanas termiņš / Время загрузки:<br>' . 
                    ($order->load_datetime ? $order->load_datetime->format('Y-m-d H:i:s') : 'Nav norādīts') . 
                '<br><strong>Nepieciešamie dokumenti / Необходимые документы:</strong><br>TIR, CMR' .
                '<br><strong>Speciāli noteikumi / Специальные условия:</strong><br>CMR страховка' .
                '</td>
                <td width="50%"><strong>Izkraušanas adreses / Адреса выгрузок:</strong><br>' . 
                    htmlspecialchars($order->unload_address ?? 'Nav norādīts') . '<br>' .
                    'Izkraušanas termiņš / Время выгрузки:<br>' . 
                    ($order->unload_datetime ? $order->unload_datetime->format('Y-m-d H:i:s') : 'Nav norādīts') . 
                '</td>
            </tr>
            <tr>
                <td>A/M Nr.: ' . htmlspecialchars($order->vehicle_number ?? '') . '</td>
                <td>Vaditajs / Водитель: ' . htmlspecialchars($order->driver_name ?? '') . '</td>
            </tr>
            <tr>
                <td>A/M marka: ' . htmlspecialchars($order->vehicle_brand ?? '') . '</td>
                <td>A/M tips: ' . htmlspecialchars($order->vehicle_type ?? '') . '</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Krāvas info / Информация о грузе:</strong> ' . 
                    htmlspecialchars($order->cargo_type ?? '') . 
                '</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Frahta vērtība, valūta / Фрахт , валюта:</strong> ' . 
                    number_format($order->freight_amount ?? 0, 2) . ' ' . ($order->currency ?? 'EUR') . 
                '</td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
    }

    private function addAdditionalInformation(TCPDF $pdf, $order)
    {
        $pdf->SetFont('dejavusans', '', 8);
        
        $additionalInfo = "Apmaksu garantējam 30 dienu laikā pēc oriģināla rēķina un CMR pavadzīmes saņemšanas . Rēķinā uzradīt pakalpojuma
sniegšanas datums! Mašīnai ir jābūt atbilstošā tehniskā kārtībā ar nepieciešamo aprīkojumu. Iekraušanai un izkraušanai ES
valstīs ir paredzētās pa 24st. Muitas noformējumam, iekraušanai un izkraušanai 48st. ES valsīs (ņemot vērā transportlīdzekļu
ierašanos līdz 9:00). Muitas noformējumam, iekraušanai un izkraušanai 72st. NVS valsīs (ņemot vērā transportlīdzekļu
ierašanos līdz 9:00). Dīkstāvi 100 euro/diennaktī, neieskaitot brīvdienas un svētku dienas . Par iebkuru kavēšanos uzreiz
jāpaziņo firmai \"SV PLUSS\". Automašīnas neierašanās gadījumā pārvadātājam jāmaksā sods 100 euro apmērā. Kravu
pārvadāšana notiek saskaņā ar CMR konvencijas noteikumiem, pārvadātājs uzņemas atbildību par kravas pilnīgu vai daļēju
nozaudēšanu vai sabojāšanu, kura ir radusies laika periodā kopš brīža, kad viņš ir pieņēmis kravu līdz tāš nodošanai. Analīžu
veikšanas laiks pēc Latvijas veterinārā dienesta pieprasījuma netiek uzskatīts par dīkstāvi un netiek apmaksāts.

Оплaту гарантируем в течении 30 дней после получения оригиналов счета и КДПГ накладной . В счете указать дату
оказания услуги! Транспортне средство должно быть технически исправно и соответствовать требованиям,
предъявляемым международным перевозкам. Время на загрузку и выгрузку в ЕС по 24 часа. Время на таможенное
оформление, загрузку/выгрузку в ЕС – 48 часов (с учетом прибытия автотранспорта до 9 утра). Время на таможенное
оформление, загрузку/выгрузку в СНГ – 72 часа (с учетом прибытия автотранспорта до 9 утра). Простой – 100
евро/cутки. Выходные/праздничные дни в простой не входят. О любой задержке незамедлительно сообщать фирме
\"SV PLUSS\". В случае не подачи транспорта на имя перевозчика выставляется штраф, в размере 100 EUR. Перевозчик
несет ответственность, согластно КДПГ конвенции, за полную или частичную утрату или повреждение груза,
происшедшее в промежуток времени между принятием груза к перевозке и до момента сдачи грузополучателю.
Простой автомашины под анализами по запросу ветеринарной службы Латвии не оплачивается .";

        $pdf->MultiCell(0, 10, $additionalInfo, 0, 'L');
    }

    private function addPartyInformation(TCPDF $pdf, $order)
    {
        $pdf->SetFont('dejavusans', '', 10);
        
        $html = '<table>
            <tr>
                <td width="50%">
                    <strong>Pasūtītājs:</strong><br>
                    SV Pluss, SIA<br>
                    Reģ. Nr. 40003259610<br>
                    Prūšu 40a-40, Rīga, LV-1057, Latvija
                </td>
                <td width="50%">
                    <strong>Pārvadātājs:</strong><br>' . 
                    htmlspecialchars($order->carrier_name ?? 'Nav norādīts') . '<br>' .
                    ($order->reg_number ? 'Reģ. Nr. ' . htmlspecialchars($order->reg_number) : '') . '<br>' .
                    htmlspecialchars($order->address ?? '')
                . '</td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // Add company stamp/signature
        $stampPath = public_path('images/svpluss_stamp.jpg');
        if (file_exists($stampPath)) {
            // Adjust the position to ensure it's on the same page
            $pdf->Image($stampPath, 15, $pdf->GetY() + 10, 50, '', '', '', '', false, 300);
        }
    }
}