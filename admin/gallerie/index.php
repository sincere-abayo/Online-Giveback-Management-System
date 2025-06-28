<!DOCTYPE html>  
<html>  
<head>  
    <title>Image Gallery </title>
    <h1>
    <span>Gallery</span>
</h1>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.cs">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>  
  
    <style type="text/css">
    h1 {
    text-align: center; 
}
h1 span { 
    background-color: whitesmoke; 
}  
    .gallery  
    {  
        display: inline-block;  
        margin-top: 20px;  
    }  
    .close-icon{  
    border-radius: 50%;  
        position: absolute;  
        right: 5px;  
        top: -10px;  
        padding: 5px 8px;  
    }  
        .form-image-upload{  
            background: #e8e8e8 none repeat scroll 0 0;  
            padding: 15px;  
        }  
    </style>  
    <style>
 
body {
  font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 18px;
  line-height: 1.42857143;
  color: white;
  background-color: #fff;
}
input,
button,
select,
textarea {
  font-family: inherit;
  font-size: inherit;
  line-height: inherit;
}
a {
  color: #f5f5f5;
  text-decoration: none;
}
a:hover,
a:focus {
  color: #23527c;
  text-decoration: underline;
}
a:focus {
  outline: 5px auto -webkit-focus-ring-color;
  outline-offset: -2px;
}
figure {
  margin: 0;
}
img {
  vertical-align: middle;
}
.img-responsive,
.thumbnail > img,
.thumbnail a > img,
.carousel-inner > .item > img,
.carousel-inner > .item > a > img {
  display: block;
  max-width: 100%;
  height: auto;
}
.img-rounded {
  border-radius: 6px;
}
.img-thumbnail {
  display: inline-block;
  max-width: 100%;
  height: auto;
  padding: 4px;
  line-height: 1.42857143;
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 4px;
  -webkit-transition: all .2s ease-in-out;
       -o-transition: all .2s ease-in-out;
          transition: all .2s ease-in-out;
}
.img-circle {
  border-radius: 50%;
}
hr {
  margin-top: 20px;
  margin-bottom: 20px;
  border: 0;
  border-top: 1px solid #eee;
}
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  border: 0;
}
.sr-only-focusable:active,
.sr-only-focusable:focus {
  position: static;
  width: auto;
  height: auto;
  margin: 0;
  overflow: visible;
  clip: auto;
}
[role="button"] {
  cursor: pointer;
}
h1,
h2,
h3,
h4,
h5,
h6,
.h1,
.h2,
.h3,
.h4,
.h5,
.h6 {
  font-family: inherit;
  font-weight: 500;
  line-height: 1.1;
  color: inherit;
}
h1 small,
h2 small,
h3 small,
h4 small,
h5 small,
h6 small,
.h1 small,
.h2 small,
.h3 small,
.h4 small,
.h5 small,
.h6 small,
h1 .small,
h2 .small,
h3 .small,
h4 .small,
h5 .small,
h6 .small,
.h1 .small,
.h2 .small,
.h3 .small,
.h4 .small,
.h5 .small,
.h6 .small {
  font-weight: normal;
  line-height: 1;
  color: #777;
}
h1,
.h1,
h2,
.h2,
h3,
.h3 {
  margin-top: 20px;
  margin-bottom: 10px;
}
h1 small,
.h1 small,
h2 small,
.h2 small,
h3 small,
.h3 small,
h1 .small,
.h1 .small,
h2 .small,
.h2 .small,
h3 .small,
.h3 .small {
  font-size: 65%;
}
h4,
.h4,
h5,
.h5,
h6,
.h6 {
  margin-top: 10px;
  margin-bottom: 10px;
}
h4 small,
.h4 small,
h5 small,
.h5 small,
h6 small,
.h6 small,
h4 .small,
.h4 .small,
h5 .small,
.h5 .small,
h6 .small,
.h6 .small {
  font-size: 75%;
}
h1,
.h1 {
  font-size: 36px;
}
h2,
.h2 {
  font-size: 30px;
}
h3,
.h3 {
  font-size: 24px;
}
h4,
.h4 {
  font-size: 18px;
}
h5,
.h5 {
  font-size: 14px;
}
h6,
.h6 {
  font-size: 12px;
}
p {
  margin: 0 0 10px;
}
.lead {
  margin-bottom: 20px;
  font-size: 16px;
  font-weight: 300;
  line-height: 1.4;
}
@media (min-width: 768px) {
  .lead {
    font-size: 21px;
  }
}
small,
.small {
  font-size: 85%;
}
mark,
.mark {
  padding: .2em;
  background-color: #fcf8e3;
}
.text-left {
  text-align: left;
}
.text-right {
  text-align: right;
}
.text-center {
  text-align: center;
}
.text-justify {
  text-align: justify;
}
.text-nowrap {
  white-space: nowrap;
}
.text-lowercase {
  text-transform: lowercase;
}
.text-uppercase {
  text-transform: uppercase;
}
.text-capitalize {
  text-transform: capitalize;
}
.text-muted {
  color: #777;
}
.text-primary {
  color: #337ab7;
}
a.text-primary:hover,
a.text-primary:focus {
  color: #286090;
}
.text-success {
  color: #3c763d;
}
a.text-success:hover,
a.text-success:focus {
  color: #2b542c;
}
.text-info {
  color: #31708f;
}
a.text-info:hover,
a.text-info:focus {
  color: #245269;
}
.text-warning {
  color: #8a6d3b;
}
a.text-warning:hover,
a.text-warning:focus {
  color: #66512c;
}
.text-danger {
  color: #a94442;
}
a.text-danger:hover,
a.text-danger:focus {
  color: #843534;
}
.bg-primary {
  color: #fff;
  background-color: #337ab7;
}
a.bg-primary:hover,
a.bg-primary:focus {
  background-color: #286090;
}
.bg-success {
  background-color: #dff0d8;
}
a.bg-success:hover,
a.bg-success:focus {
  background-color: #c1e2b3;
}
.bg-info {
  background-color: #d9edf7;
}
a.bg-info:hover,
a.bg-info:focus {
  background-color: #afd9ee;
}
.bg-warning {
  background-color: #fcf8e3;
}
a.bg-warning:hover,
a.bg-warning:focus {
  background-color: #f7ecb5;
}
.bg-danger {
  background-color: #f2dede;
}
a.bg-danger:hover,
a.bg-danger:focus {
  background-color: #e4b9b9;
}
.page-header {
  padding-bottom: 9px;
  margin: 40px 0 20px;
  border-bottom: 1px solid #eee;
}
ul,
ol {
  margin-top: 0;
  margin-bottom: 10px;
}
ul ul,
ol ul,
ul ol,
ol ol {
  margin-bottom: 0;
}
.list-unstyled {
  padding-left: 0;
  list-style: none;
}
.list-inline {
  padding-left: 0;
  margin-left: -5px;
  list-style: none;
}
.list-inline > li {
  display: inline-block;
  padding-right: 5px;
  padding-left: 5px;
}
dl {
  margin-top: 0;
  margin-bottom: 20px;
}
dt,
dd {
  line-height: 1.42857143;
}
dt {
  font-weight: bold;
}
dd {
  margin-left: 0;
}
@media (min-width: 768px) {
  .dl-horizontal dt {
    float: left;
    width: 160px;
    overflow: hidden;
    clear: left;
    text-align: right;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .dl-horizontal dd {
    margin-left: 180px;
  }
}
abbr[title],
abbr[data-original-title] {
  cursor: help;
  border-bottom: 1px dotted #777;
}
.initialism {
  font-size: 90%;
  text-transform: uppercase;
}
blockquote {
  padding: 10px 20px;
  margin: 0 0 20px;
  font-size: 17.5px;
  border-left: 5px solid #eee;
}
blockquote p:last-child,
blockquote ul:last-child,
blockquote ol:last-child {
  margin-bottom: 0;
}
blockquote footer,
blockquote small,
blockquote .small {
  display: block;
  font-size: 80%;
  line-height: 1.42857143;
  color: #777;
}
blockquote footer:before,
blockquote small:before,
blockquote .small:before {
  content: '\2014 \00A0';
}
.blockquote-reverse,
blockquote.pull-right {
  padding-right: 15px;
  padding-left: 0;
  text-align: right;
  border-right: 5px solid #eee;
  border-left: 0;
}
.blockquote-reverse footer:before,
blockquote.pull-right footer:before,
.blockquote-reverse small:before,
blockquote.pull-right small:before,
.blockquote-reverse .small:before,
blockquote.pull-right .small:before {
  content: '';
}
.blockquote-reverse footer:after,
blockquote.pull-right footer:after,
.blockquote-reverse small:after,
blockquote.pull-right small:after,
.blockquote-reverse .small:after,
blockquote.pull-right .small:after {
  content: '\00A0 \2014';
}
address {
  margin-bottom: 20px;
  font-style: normal;
  line-height: 1.42857143;
}
code,
kbd,
pre,
samp {
  font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
}
code {
  padding: 2px 4px;
  font-size: 90%;
  color: #c7254e;
  background-color: #f9f2f4;
  border-radius: 4px;
}
kbd {
  padding: 2px 4px;
  font-size: 90%;
  color: #fff;
  background-color: #333;
  border-radius: 3px;
  -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .25);
          box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .25);
}
kbd kbd {
  padding: 0;
  font-size: 100%;
  font-weight: bold;
  -webkit-box-shadow: none;
          box-shadow: none;
}
pre {
  display: block;
  padding: 9.5px;
  margin: 0 0 10px;
  font-size: 13px;
  line-height: 1.42857143;
  color: #333;
  word-break: break-all;
  word-wrap: break-word;
  background-color: #f5f5f5;
  border: 1px solid #ccc;
  border-radius: 4px;
}
pre code {
  padding: 0;
  font-size: inherit;
  color: inherit;
  white-space: pre-wrap;
  background-color: transparent;
  border-radius: 0;
}
.pre-scrollable {
  max-height: 340px;
  overflow-y: scroll;
}
.container {
  padding-right: 15px;
  padding-left: 15px;
  margin-right: auto;
  margin-left: auto;
}
@media (min-width: 768px) {
  .container {
    width: 750px;
  }
}
@media (min-width: 992px) {
  .container {
    width: 970px;
  }
}
@media (min-width: 1200px) {
  .container {
    width: 1170px;
  }
}
.container-fluid {
  padding-right: 15px;
  padding-left: 15px;
  margin-right: auto;
  margin-left: auto;
}
.row {
  margin-right: -15px;
  margin-left: -15px;
}
.col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
  position: relative;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}
.col-xs-1, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9, .col-xs-10, .col-xs-11, .col-xs-12 {
  float: left;
}
        </style>
</head>  
<body>  
  
<div class="container">  
  
     
    <form action="././gallerie/Upload.php" class="form-image-upload" method="POST" enctype="multipart/form-data">  
  
        <?php if(!empty($_SESSION['error'])){ ?>  
            <div class="alert alert-danger">  
                <strong>Whoops!</strong> Our input faces some problems. <br><br>  
                <ul>  
                    <li><?php echo $_SESSION['error']; ?></li>  
                </ul>  
            </div>  
        <?php unset($_SESSION['error']); } ?>  
  
        <?php if(!empty($_SESSION['success'])){ ?>  
        <div class="alert alert-success alert-block">  
            <button type="button" class="close" data-dismiss="alert">?</button>  
                <strong><?php echo $_SESSION['success']; ?></strong>  
        </div>  
        <?php unset($_SESSION['success']); } ?>  
  
        <div class="row">  
            <div class="col-md-5">  
                <strong>Title:</strong>  
                <input type="text" name="title" class="form-control" placeholder="Title">  
            </div>  
            <div class="col-md-5">  
                <strong>Image:</strong>  
                <input type="file" name="image" class="form-control">  
            </div>  
            <div class="col-md-2">  
                <br/>  
                <button type="submit" class="btn btn-success">Upload</button>  
            </div>  
        </div>  
  
    </form>   
  
    <div class="row">  
    <div class='list-group gallery'>  
  
            <?php  
            require('db_config.php');  
  
            $sql = "SELECT * FROM gallery";  
            $images = $conn->query($sql);  
  
            while($image = $images->fetch_assoc()){  
  
            ?> 
                <div class='col-sm-4 col-xs-6 col-md-3 col-lg-3'>  
                    <a class="thumbnail fancybox" rel="ligthbox" href="././gallerie/uploads/<?php echo $image['image'] ?>">  
                        <img class="img-responsive" alt="" src="././gallerie/uploads/<?php echo $image['image'] ?>" />  
                        <div class='text-center'>  
                            <small class='text-muted'><?php echo $image['title'] ?></small>  
                        </div> <!-- text-center / end -->  
                    </a>  
                    <form action="././gallerie/Delete.php" method="POST">  
                    <input type="hidden" name="id" value="<?php echo $image['id'] ?>">  
                    <button type="submit" class="close-icon btn btn-danger" >
                      <img  src="././gallerie/uploads/x.png" style="width:20px; height:20px;" >
                    </button>
                    </form>  
                </div> <!-- col-6 / end -->  
            <?php } ?>  
  
        </div> <!-- list-group / end -->  
    </div> <!-- row / end -->  
</div> <!-- container / end -->  
  
</body>  
  
<script type="text/javascript">  
    $(document).ready(function(){  
        $(".fancybox").fancybox({  
            openEffect: "none",  
            closeEffect: "none"  
        });  
    });  
</script>  
</html>


