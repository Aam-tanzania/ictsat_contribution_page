<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ICTSAT Contribution</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card{
border-radius:15px;
box-shadow:0px 10px 25px rgba(0,0,0,0.08);
}

.club-title{
font-weight:700;
color:#0d6efd;
}

</style>

</head>

<body>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card p-4">

<h3 class="text-center club-title">ICTSAT Club</h3>
<p class="text-center text-muted">TEKU Contribution</p>

<form action="process_payment.php" method="POST">

<div class="mb-3">
<label class="form-label">Contributor Name</label>
<input type="text" name="name" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Phone Number</label>
<input type="text" name="phone" class="form-control" placeholder="07XXXXXXXX" required>
</div>

<div class="d-grid">
<button class="btn btn-primary btn-lg">
Contribute 1000 TSH
</button>
</div>

</form>

</div>

</div>

</div>

</div>

</body>
</html>