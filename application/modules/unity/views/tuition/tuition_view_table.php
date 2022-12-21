<table class="table-auto">
    <thead>
        <tr>
        <th>Tuition:</th>
        <td><?php echo $tuition['tuition']; ?></td>      
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Miscellaneous:</th>
            <td></td>      
        </tr>
    <?php foreach($tuition['misc_list'] as $key=>$val): ?>
        <tr>
            <th><?php echo $key; ?></th>
            <td><?php echo $val; ?></td>      
        </tr>
    <?php endforeach; ?>
        <tr>
            <th>Total:</th>
            <td><?php echo $tuition['misc']; ?></td>      
        </tr>    
        <tr>
            <th>Laboratory Fee:</th>
            <td class="text-green"></td>
        </tr>
        <hr />                
        <?php foreach($tuition['lab_list'] as $key=>$val): ?>
            <tr>
                <th><?php echo $key; ?></th>
                <td><?php echo $val; ?></td>
            </tr>
        <?php endforeach; ?>        
        <tr>
            <th>Total:</th>
            <td class="text-green"><?php echo $tuition['lab']; ?></td>
        </tr>
        <hr />
        <?php if($tuition['thesis_fee']!= 0): ?>                    
            <tr>
                <th>THESIS FEE: </th>
                <td class="text-green"><?php echo $tuition['thesis_fee']; ?></td>
            </tr>
            <hr />                    
        <?php endif; ?>
        <?php if($tuition['internship_fee']!= 0): ?>
            
            <tr>
                <th>Internship Fees:</th>
                <td class="text-green"></td>
            </tr>
            <hr />
            
            <?php foreach($tuition['internship_fee_list'] as $key=>$val): ?>
                <tr>
                    <th><?php echo $key; ?></th>
                    <td><?php echo $val; ?></td>
                </tr>
            <?php endforeach; ?>

            
            <tr>
                <th>Total:</th>
                <td class="text-green"><?php echo $tuition['internship_fee']; ?></td>
            </tr>
            <hr />
        <?php endif; ?>
        <?php if($tuition['new_student']!= 0): ?>
            <tr>
                <th>New Student Fees:</th>
                <td class="text-green"></td>
            </tr>
            <hr />
            
            <?php foreach($tuition['new_student_list'] as $key=>$val): ?>                
                <tr>
                    <th><?php echo $key; ?></th>
                    <td><?php echo $val; ?></td>
                </tr>
            <?php endforeach; ?>

            
            <tr>
                <th>Total:</th>
                <td class="text-green"><?php echo $tuition['new_student']; ?></td>
            </tr>
            <hr />
        <?php endif; ?>
            
        <tr>
            <th>Total:</th>
            <td class="text-green"><?php echo $tuition['total'] ?></td>
        </tr>
        <hr />
        <h4 class="box-title">FOR INSTALLMENT</h4>
        <tr>
            <th>Down Payment</th>
            <td><?php echo number_format($tuition['down_payment'], 2, '.' ,','); ?></td>
        </tr>
        <br />
        <?php for($i=0;$i<5;$i++): ?>
        <tr>        
            <th><?php echo switch_num($i + 1) ?> INSTALLMENT</th>
            <td><?php echo number_format($tuition['installment_fee'], 2, '.' ,','); ?></td>            
        </tr>
        <?php endfor; ?>
        <hr />
        <tr>
            <th>Total for installment</th>
            <td class="text-green"><?php echo number_format($tuition['total_installment'], 2, '.' ,','); ?></td>
        </tr>   
    </tbody>
</table>
