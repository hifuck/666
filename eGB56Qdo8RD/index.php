<?php 
include 'global.php';
?>

<?php include 'base.php';?>

<?php 

?>

<!-- page start -->
<div class="content">
    <div class="header">        
        <h1 class="page-title">控制面板</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li class="active">控制面板</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid">
           
            
            <footer>
                <hr>
                <p class="pull-right">
                    <a href="javascript:;">
                        <?php echo $appSet[ 'app_name'];?>
                    </a>
                </p>
                <p>&copy;
                    <?php echo $appSet[ 'company_year'];?>
                    <a href="<?php echo $appSet['company_url'];?>" title="<?php echo $appSet['company'];?>" target="_blank">
                        <?php echo $appSet[ 'company'];?>
                    </a>
                </p>
            </footer>
        </div>
    </div>
</div>

<!-- page end -->

<?php include 'foot.php';?>
