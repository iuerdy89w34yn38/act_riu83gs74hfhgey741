<!doctype html>
<html>
<head>
  <?php include"include/connect.php" ?>
  <?php include"include/head.php" ?>

  <title>Sales Return  - <?php echo $comp_name ?>  </title>


      <style type="text/css">
      .mycon {
        border: 1px solid #cacfe7;
        color: #3b4781;
        border-radius: 0.25rem;
        padding: 8px 10px;
      }
      .table th, .table td {
        padding: 5px 6px;
        text-align: center;
        vertical-align: middle;
      }
    </style>
  </head>
  <body class="vertical-layout vertical-menu-modern 2-columns   menu-expanded fixed-navbar"
  data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">


  <?php if(isset($_POST['send'])){








    $act = $_POST['act'];
    $pay = $_POST['pay'];
    $reason = $_POST['reason'];

    $rows =mysqli_query($con,"SELECT * FROM customers where id=$act ORDER BY name" ) or die(mysqli_error($con));
    while($row=mysqli_fetch_array($rows)){ 
      $actname = $row['name'];
      $actbalance = $row['balance'];
      $acttype = $row['type'];
      $acttypeid = $row['typeid'];

    }

    $chequeno = $_POST['chequeno'];
    $chequeamt = $_POST['chequeamt'];


    $subt = $_POST['sub_total'];
    $amount = preg_replace("/[^0-9^.]/", '', $subt); 

    $datec=date('Y-m-d');
    $dateup=date('Y-m-d');



    if($pay=='credit'){

      $srcid=200029;
      $rows =mysqli_query($con,"SELECT * FROM acts where id=$srcid ORDER BY name" ) or die(mysqli_error($con));
      while($row=mysqli_fetch_array($rows)){ 
        $srcname = $row['name'];
        $srcbalance = $row['balance'];
        $srctype = $row['type'];
        $srctypeid = $row['typeid'];

      }

      $destid=$act;
      $rows =mysqli_query($con,"SELECT * FROM customers where id=$destid ORDER BY name" ) or die(mysqli_error($con));
      while($row=mysqli_fetch_array($rows)){ 
        $destname = $row['name'];
        $destbalance = $row['balance'];
        $desttype = $row['type'];
        $desttypeid = $row['typeid'];
      }

      //First Entry

      $srcbalance=$srcbalance+$amount;
      $destbalance=$destbalance-$amount;




      $desp='Sales Return from '.$destname.' to '.$srcname.' due to '.$reason;

                    //transaction Entry
      $data=mysqli_query($con,"INSERT INTO transaction (desp,dract,cract,cr,dr,datec,dateup)VALUES ('$desp','$destid','$srcid','$amount','$amount','$datec','$dateup')")or die( mysqli_error($con) );


  $sqls = "UPDATE acts SET `balance` = '$srcbalance' WHERE `id` = $srcid"  ;
  mysqli_query($con, $sqls)or die(mysqli_error($con));

  $sqls = "UPDATE customers SET `balance` = '$destbalance' WHERE `id` = $destid"  ;
  mysqli_query($con, $sqls)or die(mysqli_error($con));



                    //Ledger Entry
      $rows =mysqli_query($con,"SELECT id FROM transaction ORDER BY id desc limit 1" ) or die(mysqli_error($con));
      while($row=mysqli_fetch_array($rows)){ 
        $jid = $row['id'];

      }


      $desp='Credit Sale Return';

      $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,balance,dr,datec,dateup)VALUES ('$jid','$srcid','$desp','$srctype','$srctypeid','$srcbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

      $desp='Sales Return from '.$destname.' to '.$srcname.' due to '.$reason;

      $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,balance,cr,datec,dateup)VALUES ('$jid','$destid','$desp','$desttype','$desttypeid','$destbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );


    }
    else{


      $destid=$act;
      $srcid=$pay;

      if($srcid==200019){ //Cash in Hands

          $rows =mysqli_query($con,"SELECT * FROM acts where id=$srcid ORDER BY name" ) or die(mysqli_error($con));
          while($row=mysqli_fetch_array($rows)){ 
            $srcname = $row['name'];
            $srcbalance = $row['balance'];
            $srctype = $row['type'];
            $srctypeid = $row['typeid'];
          }


          $destid=200029;
          $rows =mysqli_query($con,"SELECT * FROM acts where id=$destid ORDER BY name" ) or die(mysqli_error($con));
          while($row=mysqli_fetch_array($rows)){ 
            $destname = $row['name'];
            $destbalance = $row['balance'];
            $desttype = $row['type'];
            $desttypeid = $row['typeid'];

          }

            //First Entry
    

         $srcbalance=$srcbalance-$amount;
         $destbalance=$destbalance+$amount;




         $desp='Sales Return from '.$destname.' to '.$srcname.' due to '.$reason;

                          //transaction Entry
         $data=mysqli_query($con,"INSERT INTO transaction (desp,dract,cract,cr,datec,dateup,invoiceno,invoicepic)VALUES ('$desp','$destid','$srcid','$amount','$datec','$dateup','$invoiceno','$invoicepic')")or die( mysqli_error($con) );


         $sqls = "UPDATE acts SET `balance` = '$srcbalance' WHERE `id` = $srcid"  ;
         mysqli_query($con, $sqls)or die(mysqli_error($con));

         $sqls = "UPDATE acts SET `balance` = '$destbalance' WHERE `id` = $destid"  ;
         mysqli_query($con, $sqls)or die(mysqli_error($con));






                          //Ledger Entry
         $rows =mysqli_query($con,"SELECT id FROM transaction ORDER BY id desc limit 1" ) or die(mysqli_error($con));
         while($row=mysqli_fetch_array($rows)){ 
          $jid = $row['id'];

        }


         $desp='Sales Return from '.$destname.' to '.$srcname.' due to '.$reason;


        $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,balance,cr,datec,dateup)VALUES ('$jid','$srcid','$desp','$srctype','$srctypeid','$srcbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

        $desp=$srcname.' Sale Returns';

        $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,ref,balance,dr,datec,dateup)VALUES ('$jid','$act','$desp','$acttype','$acttypeid',1,'$actbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

         $desp='Sales Return from '.$destname.' to '.$srcname.' due to '.$reason;


        $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,ref,balance,cr,datec,dateup)VALUES ('$jid','$act','$desp','$acttype','$acttypeid',1,'$actbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

        $desp=$srcname.' Sale Returns';

        $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,balance,dr,datec,dateup)VALUES ('$jid','$destid','$desp','$desttype','$desttypeid','$destbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

      }
      else if($srcid==200032){ //Cheque 

        
          $rows =mysqli_query($con,"SELECT * FROM acts where id=$srcid ORDER BY name" ) or die(mysqli_error($con));
          while($row=mysqli_fetch_array($rows)){ 
            $srcname = $row['name'];
            $srcbalance = $row['balance'];
            $srctype = $row['type'];
            $srctypeid = $row['typeid'];
          }
          $destid=200029;
          $rows =mysqli_query($con,"SELECT * FROM acts where id=$destid ORDER BY name" ) or die(mysqli_error($con));
          while($row=mysqli_fetch_array($rows)){ 
            $destname = $row['name'];
            $destbalance = $row['balance'];
            $desttype = $row['type'];
            $desttypeid = $row['typeid'];

          }

            //First Entry
    

         $srcbalance=$srcbalance-$chequeamt;
         $destbalance=$destbalance+$amount;
         $chequebal=$amount-$chequeamt;
         $actbalance=$actbalance-$chequebal;




         $desp='Sales Return from '.$destname.' to '.$srcname.' No. '.$chequeno.' due to '.$reason;

                          //transaction Entry
         $data=mysqli_query($con,"INSERT INTO transaction (desp,dract,cract,cr,datec,dateup,invoiceno,invoicepic,chequeno,chequeamt)VALUES ('$desp','$destid','$srcid','$amount','$datec','$dateup','$invoiceno','$invoicepic','$chequeno','$chequeamt')")or die( mysqli_error($con) );


         $sqls = "UPDATE acts SET `balance` = '$srcbalance' WHERE `id` = $srcid"  ;
         mysqli_query($con, $sqls)or die(mysqli_error($con));

         $sqls = "UPDATE acts SET `balance` = '$destbalance' WHERE `id` = $destid"  ;
         mysqli_query($con, $sqls)or die(mysqli_error($con));

         $sqls = "UPDATE customers SET `balance` = '$actbalance' WHERE `id` = $act"  ;
         mysqli_query($con, $sqls)or die(mysqli_error($con));






                          //Ledger Entry
         $rows =mysqli_query($con,"SELECT id FROM transaction ORDER BY id desc limit 1" ) or die(mysqli_error($con));
         while($row=mysqli_fetch_array($rows)){ 
          $jid = $row['id'];

        }


        $desp='Sales Return from '.$destname.' to '.$srcname.' No. '.$chequeno.' due to '.$reason;


        $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,balance,cr,datec,dateup)VALUES ('$jid','$srcid','$desp','$srctype','$srctypeid','$srcbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

        $desp=$srcname.' Sale Returns';

        $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,ref,balance,dr,datec,dateup)VALUES ('$jid','$act','$desp','$acttype','$acttypeid',1,'$actbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

        $desp='Sales Return from '.$destname.' to '.$srcname.' No. '.$chequeno.' due to '.$reason;


        $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,ref,balance,cr,datec,dateup)VALUES ('$jid','$act','$desp','$acttype','$acttypeid',1,'$actbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

        $desp=$srcname.' Sales Returns';

        $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,balance,dr,datec,dateup)VALUES ('$jid','$destid','$desp','$desttype','$desttypeid','$destbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

      }
      else{
        $destid=200029;

      $rows =mysqli_query($con,"SELECT * FROM acts where id=$srcid ORDER BY name" ) or die(mysqli_error($con));
      while($row=mysqli_fetch_array($rows)){ 
        $srcname = $row['name'];
        $srcbalance = $row['balance'];
        $srctype = $row['type'];
        $srctypeid = $row['typeid'];
      }

      
      $rows =mysqli_query($con,"SELECT * FROM acts where id=$destid ORDER BY name" ) or die(mysqli_error($con));
      while($row=mysqli_fetch_array($rows)){ 
        $destname = $row['name'];
        $destbalance = $row['balance'];
        $desttype = $row['type'];
        $desttypeid = $row['typeid'];

      }

        //First Entry
 
      $srcbalance=$srcbalance-$amount;
      $destbalance=$destbalance+$amount;




      $desp='Sales Return from '.$destname.' to '.$srcname.' due to '.$reason;

                      //transaction Entry
     $data=mysqli_query($con,"INSERT INTO transaction (desp,dract,cract,cr,dr,datec,dateup,invoiceno,invoicepic)VALUES ('$desp','$destid','$srcid','$amount','$amount','$datec','$dateup','$invoiceno','$invoicepic')")or die( mysqli_error($con) );


     $sqls = "UPDATE acts SET `balance` = '$srcbalance' WHERE `id` = $srcid"  ;
     mysqli_query($con, $sqls)or die(mysqli_error($con));

     $sqls = "UPDATE acts SET `balance` = '$destbalance' WHERE `id` = $destid"  ;
     mysqli_query($con, $sqls)or die(mysqli_error($con));






                      //Ledger Entry
     $rows =mysqli_query($con,"SELECT id FROM transaction ORDER BY id desc limit 1" ) or die(mysqli_error($con));
     while($row=mysqli_fetch_array($rows)){ 
      $jid = $row['id'];

    }



    $desp='Sales Return from '.$destname.' to '.$srcname.' due to '.$reason;


    $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,balance,cr,datec,dateup)VALUES ('$jid','$srcid','$desp','$srctype','$srctypeid','$srcbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

    $desp=$srcname.' Sales Returns';

    $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,ref,balance,dr,datec,dateup)VALUES ('$jid','$act','$desp','$acttype','$acttypeid',1,'$actbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

    $desp='Sales Return from '.$destname.' to '.$srcname.' due to '.$reason;


    $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,ref,balance,cr,datec,dateup)VALUES ('$jid','$act','$desp','$acttype','$acttypeid',1,'$actbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

    $desp=$srcname.' Sales Returns';

    $data=mysqli_query($con,"INSERT INTO journal (jid,actid,desp,type,typeid,balance,dr,datec,dateup)VALUES ('$jid','$destid','$desp','$desttype','$desttypeid','$destbalance','$amount','$datec','$dateup')")or die( mysqli_error($con) );

    }

  }


  $x= count($home = $_POST['qty1']);

  for ($i=0; $i < $x; $i++) {

    $pid = $_POST['item'][$i];
    $qty = $_POST['qty1'][$i];
    $price = $_POST['price1'][$i];



    $rows =mysqli_query($con,"SELECT name,stock FROM items where id=$pid" ) or die(mysqli_error($con));

    while($row=mysqli_fetch_array($rows)){

      $name = $row['name'];
      $stock = $row['stock'];
    }

    $stock=$stock+$qty;
    $type='in';
    $date=date('Y-m-d');


                      //transaction Entry
    $data=mysqli_query($con,"INSERT INTO itemslog (jid,pid,type,name,price,quantity,subtotal,datec)VALUES ('$jid','$pid','$type','$name','$price','$qty','$amount','$date')")or die( mysqli_error($con) );

    $sqls = "UPDATE items SET `stock` = '$stock' WHERE `id` = $pid"  ;
    mysqli_query($con, $sqls)or die(mysqli_error($con));


  }
  $msg = 'Successfull';





}

?>

<?php $link="rtnsale.php"; ?>

<?php include"include/header.php" ?>
<?php include"include/sidebar.php" ?>

<div class="app-content content">
 <div class="content-wrapper">

  <form name="cart" method="POST" action="" enctype="multipart/form-data">


    <div class="col-sm-12">
      <div class="card">
        <div class="card-header" style="padding-bottom: 0px;">
          <h4 class="card-title">Make Purchase Invoice</h4>
        </div>
        <div class="card-block">
          <div class="card-body">

            <div class="row">

              <div class="col-sm-1">

              </div>
              <div class="col-sm-3">
                <center><span>Date:</span></center>
                <input readonly type="date" class="form-control" name="date" value="<?php echo date('Y-m-d') ?>">
              </div>

              <div class="col-sm-3">
                <center><span>Select Customer Account:</span></center>
                <select class="form-control select2" name="act">

                  <?php

                  $rows =mysqli_query($con,"SELECT * FROM customers  ORDER BY name" ) or die(mysqli_error($con));

                  while($row=mysqli_fetch_array($rows)){

                    $id = $row['id'];
                    $name = $row['name']; ?>

                    <option value="<?php echo $id ?>"><?php echo $name ?></option>

                  <?php } ?>

                </select>

              </div>
              <div class="col-sm-3">
                <center><span>Payment Type:</span></center>
                <select class="form-control select2" id="multiOptions" name="pay">
                  <option value="credit">Credit</option>

                  <?php

                  $rows =mysqli_query($con,"SELECT * FROM acts WHERE purpose ='cash'  ORDER BY name" ) or die(mysqli_error($con));

                  while($row=mysqli_fetch_array($rows)){

                    $id = $row['id'];
                    $name = $row['name']; ?>

                    <option value="<?php echo $id ?>"><?php echo $name ?></option>

                  <?php } ?>

                </select>

              </div>
            </div>
            <div class="row">



              <div class="col-sm-2">
              </div>
              <div class="col-sm-3">
                <center><span>Invoice ID :</span></center>
                <input type="text" name="invoiceno" class="form-control">

              </div>
              <div class="col-sm-5">
                <center><span>Invoice Pic :</span></center>
                <input type="file" name="invoicepic" class="form-control">

              </div>
            </div>

            <div class="row" id="chequediv">
              <div class="col-sm-3">
              </div>
              <div class="col-sm-3">
                <center><span>Cheque No :</span></center>
                <input type="text" name="chequeno" class="form-control">

              </div>
              <div class="col-sm-3">
                <center><span>Cheque Amount :</span></center>
                <input type="text" name="chequeamt" class="form-control">

              </div>


            </div>


            <div class="row">

              <div class="col-sm-3">

              </div>
              <div class="col-sm-6">
                <center><span>Reason:</span></center>
                <input type="text" class="form-control" name="reason" value="">
              </div>

            </div>
            <hr>

                         
                         <style type="text/css">
                           table>thead>tr>th:nth-child(2){
                         display: none;
                           }
                           table>tbody>tr>td:nth-child(2){
                         display: none;
                           }
                           
                           table>tfoot>tr>td:nth-child(2){
                         display: none;
                           }

                         </style>


                          <table style="min-width: 700px;">
                            <thead>
                              <tr>
                                <th style="min-width: 300px;">Product Name  </th>
                                <th>Selling Price</th>
                                <th>Quantity </th>
                                <th>Price </th>
                                <th>Total </th>
                                <th>Add </th>
                              </tr>
                            </thead>
                            <tbody>
                             
                             <tr><td> <select class="form-control select2" name="item[]"> <?php $rows =mysqli_query($con,"SELECT * FROM items WHERE pause =0 ORDER BY name" ) or die(mysqli_error($con)); while($row=mysqli_fetch_array($rows)){ $id = $row['id']; $brand = $row['brand']; $name = $row['name']; ?> <option value="<?php echo $id ?>"><?php $rows1 =mysqli_query($con,"SELECT * FROM itemsb WHERE id=$brand ORDER BY name" ) or die(mysqli_error($con));while($row1=mysqli_fetch_array($rows1)){ $bname = $row1['name']; ?><?php echo $bname ?> <?php echo $name ?></option><?php } } ?></select> </td><td><input class="form-control" type="number" name="pprice[]" id="pprice" value=""></td><td><input class="form-control"  type="number" oninput="wrt('0')" name="qty1[]" ></td><td><input  class="form-control"  type="number" oninput="wrt('0')" name="price1[]" ></td><td><input class="form-control"  type="number" name="item_total1[]" id="c" disabled="" ></td><td>  <button class="add_form_field btn btn-primary" type="button"><i class="la la-plus"></i> </button>
            </td></tr>




                            </tbody>

                            <tfoot>
                              <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right;">Grand Total:</td>
                                <td><input class="form-control" type="text" name="sub_total" id="d" value="0" readonly=""  ></td>
                              
                            </tfoot>
                          </table>
                        
                        


            <hr>
            <center><button name="send"  class="btn btn-primary">Add</button></center>
            

            <center><h2>

              <?php if(!empty($msg)) { ?>

                <br>
                <hr>
                <br>
                <?php echo $msg ; } ?></h2></center>
              </div>
            </div>
          </div>
        </div>

      </form>




    </div>
  </div>




</body>



<?php include 'include/footer.php'; ?>

</html>
