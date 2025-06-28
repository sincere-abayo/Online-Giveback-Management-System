
<head>

<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">

    <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');


.my-body{
background: linear-gradient(to right, rgba(235,224,232,1) 52%,rgba(254,191,1,1) 53%,rgba(254,191,1,1) 100%);
font-family: 'Roboto', sans-serif;
}

.card{
    border: none;
    max-width: 450px;
    border-radius: 15px;
    margin: 165px 0 165px;
    padding: 35px;
    padding-bottom: 20px!important;
}
.heading{
    color: #C1C1C1;
    font-size: 14px;
    font-weight: 500;
}

img:hover{
    cursor: pointer;
}
.text-warning{
    font-size: 12px;
    font-weight: 500;
    color: #edb537!important;
}
#cno{
    transform: translateY(-10px);
}
input{
    border-bottom: 1.5px solid #E8E5D2!important;
    font-weight: bold;
    border-radius: 0;
    border: 0;

}
.form-group input:focus{
    border: 0;
    outline: 0;
}
.col-sm-5{
    padding-left: 90px;
}
.btn:focus{
    box-shadow: none;
}
    </style>

</head>
<div class="container-fluid my-body">
    <div class="row d-flex justify-content-center">
        <div class="col-sm-12">
            <div class="card mx-auto">
                <p class="heading">Use MTN / Airtel</p>
                <p class="heading">__MTN__</p>
                    <form class="card-details " action="checkout_op.php" method="post">
                    <input type="hidden" id="provider" name="provider" value="mtn" />
                    <div class="form-group mb-0">
                    <p class="text-warning mb-0">Full Name</p>
                    <input type="text" name="fullname" placeholder="Enter your full name" class="" value="" required>
                    </div>

                        <div class="form-group mb-0">
                                <p class="text-warning mb-0">Phone Number</p> 
                                <input type="tel" name="card-num" placeholder="+250-78X-XXX-XXX" size="15" id="cno" minlength="10" maxlength="10" value="" required>

                                <img onclick="location.href='checkout_air.php'; " src="airtel.png" class="mx-2" width="64px" height="60px" />
                                
                                <img onclick="location.href='checkout_mtn.php?'; " src="mtn.png" class="mx-3" width="64px" height="60px" />

                        </div>

                        <div class="form-group">
                            <p class="text-warning mb-0">Amount RWF</p> <input type="text" name="amount" placeholder="" value="" required>
                        </div>
                        <div class="form-group pt-2">
                            <div class="row d-flex">
                                <div class="col-sm-4">
                                    
                                </div>
                                <div class="col-sm-3">
                                   
                                </div>
                                <div class="col-sm-5 pt-0">
                                    <button onclick="this.innerHTML='Tap Accept Payment...';" type="submit" name="checkout_op" class="btn btn-primary"><i class="fas fa-arrow-right px-3 py-2"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
<script>
function setProvider(name) {
    document.getElementById('provider').value = name;
}
</script>