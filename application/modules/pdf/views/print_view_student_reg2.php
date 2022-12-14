<?php

    tcpdf();
    // create new PDF document
    //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    //$pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle($student['strLastname'] . ", " . $student['strFirstname'] . ', ' . substr($student['strMiddlename'], 0,1). ".-". $student['strProgramCode']);
    
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //$pdf->SetAutoPageBreak(TRUE, 6);
    
   //font setting
    //$pdf->SetFont('calibril_0', '', 10, '', 'false');
    
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();
    $payment_division = $tuition['total'] / 4;    

    
    // Set some content to print
$html = '<table border="0" cellpadding="0" style="color:#333; font-size:10;">
        <tr>
            <td width="100%" align="center" style="text-align:center;vertical-align: middle;"><img src= "https://i.ibb.co/XW1DRVT/iacademy-logo.png"  width="150" height="44"/></td>
        </tr>
        <tr>            
            <td colspan = "3" width="100%" style="text-align: center; vertical-align: middle; line-height:100%">             
             <font style="font-family:Calibri Light; line-height: 2; font-size: 16;font-weight: bold;">Information & Communications Technology Academy </font><br />
			 <font style="font-family:Calibri Light; font-size: 10;">Filinvest Cebu Cyberzone Tower 2 Salinas Drive corner W. Geonzon St., Brgy. Apas, Lahug, Cebu City</font><br />
             <font style="font-family:Calibri Light; font-size: 10;">Telephone No: (046) 483-0470 / (046) 483-0672</font><br />
            </td>           
        </tr>
        <tr>
            <td colspan = "3" style="font-weight: bold;text-align:center; font-size:12;">ASSESSMENT/REGISTRATION FORM</td>
        </tr>
        <tr>
            <td colspan = "3" style="text-align:center; color:black; font-size: 10;"></td>
        </tr>
        <tr>
        <td colspan="3" style="font-size:10;">
        </td>
        </tr>
    </table>
     <br />
    <table border="0" cellpadding="0" style="color:#014fb3; font-size:9; border: 0px solid #014fb3;" width="528px">     
     <tr>
      <td width="80px" >&nbsp;</td>
      <td width="250px">&nbsp;</td>
      <td width="113px"></td>
      <td width="85px" style="color: black;"></td>
      
     </tr>
     <tr>
      <td width="80px">&nbsp;NAME</td>
      <td width="200px" style="color: black;">:&nbsp;' . strtoupper($student['strLastname']) . ", " . strtoupper($student['strFirstname']) . " " . substr($student['strMiddlename'], 0,1) . ".".'</td>
      <td width="80px">&nbsp;DATE</td>
      <td width="200px" style="color: black;">:&nbsp;'. $registration['dteRegistered']. '</td>      
     </tr>
     <tr>
      <td width="80px" >&nbsp;PROGRAM</td>
      <td width="200px" style="color black;">:&nbsp;'.$student['strProgramDescription'] . '</td>      
      <td width="80px" >&nbsp;STUD NO</td>
      <td width="200px" style="color: black;">:&nbsp;' . $student['strStudentNumber']. '</td>
     </tr>
     <tr>
      <td width="80px" >&nbsp;MAJOR</td>
      <td width="200px" style="color:black;">:&nbsp;' .$student['strMajor'] . '</td>
      <td width="80px" >&nbsp;SY/TERM</td>
      <td width="200px" style="color: black;text-transform:capitalize;">:&nbsp; A.Y. ' .$active_sem['strYearStart']."-".$active_sem['strYearEnd'] . ", " . $active_sem['enumSem'].' Term' . '</td>
     </tr>
     <tr>
        <td >&nbsp;</td>
        <td>&nbsp;</td>
        <td >&nbsp;</td>
        <td>&nbsp;</td>
     </tr>
    </table> '; 
$html.= '<table border="0" cellpadding="0" style="color:#014fb3; font-size:8; border: 0px solid #014fb3;" width="528">
   
        <tr>
            <th width="80px" style="text-align:left;">SECTION</th>
            <th width="65px" style="text-align:lef;">COURSE CODE</th>
            <th width="180px" style="text-align:center;">COURSE DESCRIPTION</th>
            <th width="30px" style="text-align:center;">UNITS</th>
            <th width="173px" style="text-align:center;">SCHEDULE</th>
        </tr> ';
        $html.= '
                <tr><td colspan="5"> </td> </tr>
                <tr>
                    <td colspan="5" rowspan="24" height="210px">';

                        $html.= '<table border="0" cellpadding="0" style="color:#014fb3; font-size:8;" width="528">';
                        $totalUnits = 0;
                        if (empty($records)){
                            $html.='<tr style="color: black; border-bottom: 0px solid gray;">
                                                    <td colspan="7" style="text-align:center;font-size: 11px;">No Data Available</td>
                                                </tr>';
                        }
                        else {
                                foreach($records as $record) {

                                    $html.='<tr style="color: black;">
                                            <td width="80px"> ' . $record['strSection'].'</td>
                                            <td width="65px"> '.  $record['strCode'] . '</td>
                                            <td width="180px" align ="left"> '. $record['strDescription']. '</td>
                                            <td width="30px" align = "center"> '. $record['strUnits']. '</td> ';
                                            $html.= '<td width="173px">';

                                        foreach($record['schedule'] as $sched) {
                                            if(!empty($record['schedule']))

                                                $html.= date('g:ia',strtotime($sched['dteStart'])).'  '.date('g:ia',strtotime($sched['dteEnd']))." ".$sched['strDay']." ".$sched['strRoomCode'] . " ";                    
                                        }
                                            $html.= '</td>';
                                        $html.='</tr>';
                                }
                        }
                        $html.= '</table>';

            $html.= '</td> 
            </tr>';
                                $units = 0;
                                $totalUnits = 0;
                                $totalLab = 0;
                                $totalLec = 0;
                                $lec = 0;
                                $lecForLab = 0;
                                $totalNoSubjects = 0;
                                $noOfSubjs = 0;
                         if (empty($records)) {
                                    $msg = "no data";
                                }
                        else {
                                foreach($records as $record){
                                    $noOfSubjs++;
                                    $units += $record['strUnits'];
                                        if($record['intLab']  > 0)
                                        {
                                            $totalLab += ceil($record['intLab']/3);
                                        }

                                        $lecForLab = $totalLab * 2;
                                        $lec = $units - $lecForLab;
                                        $totalLec = $totalLab + $lec;
                                    }     
                                }    

                         
        $html.='</table>
                <table border="0" cellpadding="0" style="color:#014fb3; font-size:8; border: 0px solid #014fb3;" width="528px">
        
                    <tr style="background-color:#ffff99 ; font-weight:bold;">
                    <td width="80px">&nbsp;SUBJECTS: </td>
                    <td width="75px" style="color: black;text-align:left;">' . $noOfSubjs . '</td>
                    <td width="80px">&nbsp;LEC. UNITS: </td>
                     <td width="45px" style="color: black;text-align:center;">'. $totalLec . '</td>
                    <td width="55px">&nbsp;LAB UNITS: </td>
                    <td width="45px" style="color: black;text-align:center;">' . $totalLab . '</td>
                    <td width="70px">&nbsp;TOTAL CREDITS: </td>
                    <td width="78px" style="color: black;text-align:center;">' . $units . '</td>
                    </tr>

        </table>
        
        <table border="0" cellpadding="0" style="color:#014fb3; font-size:8; border: 0px solid #014fb3;" width="528px">
        <tr>
         <td colspan ="3" style="text-align:center; background-color: #014fb3; color:white; font-size:10;">
             BILLING INFORMATION
         </td>
     </tr>
     
     <tr style="border: 0px solid #014fb3;">
         <td width="235" style="border: 0px solid #014fb3;"> SCHOLARSHIP GRANT:</td>
         <td width="293" style="border: 0px solid #014fb3;">
             PAYMENT DETAILS:
         </td>
     </tr>
     <tr style="border: 0px solid #014fb3;">
         <td width="235" style="text-align:center; color:black; border: 0px solid #014fb3;" > ' . strtoupper($student['enumScholarship']). '</td>
         <td> &nbsp;TERMS OF PAYMENT</td>
        
     </tr>
     <tr>
         <td width="235" style="border: 0px solid #014fb3;"> ASSESSMENT OF FEES:</td>
         <td width="145"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;First Payment: </td>
         <td width="148" style="text-align:center; color:black;">' . number_format($payment_division, 2, '.' ,',') . ' </td>
     </tr>
     
                
      
     </table>
        
    ';

$html = utf8_encode($html);
$pdf->writeHTML($html);

//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($student['strLastname'] . ", " . $student['strFirstname'] . ', ' . substr($student['strMiddlename'], 0,1). ".-". $student['strProgramCode'] . ".pdf", 'I');


?>