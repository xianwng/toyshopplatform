<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Profile | Titan</title>
  <link rel="apple-touch-icon" sizes="57x57" href="assets/images/favicons/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="assets/images/favicons/apple-icon-60x60.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicons/favicon-32x32.png">
  <link href="assets/lib/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Volkhov:400i" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
  <link href="assets/lib/animate.css/animate.css" rel="stylesheet">
  <link href="assets/lib/components-font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link id="color-scheme" href="assets/css/colors/default.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
</head>
<body>
<main>
  <nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
    <div class="container">
      <div class="navbar-header">
        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#custom-collapse">
          <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.html">Titan</a>
      </div>
      <div class="collapse navbar-collapse" id="custom-collapse">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="index.html">Home</a></li>
          <li><a href="shop.html">Shop</a></li>
          <li><a href="cart.html">Cart</a></li>
          <li class="active"><a href="profile.html">Profile</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="profile-container">
    <div class="container">
      <div class="profile-header">
        <img src="https://via.placeholder.com/120x120/007bff/ffffff?text=JD" alt="Profile Picture" class="profile-avatar">
        <h2 class="profile-name">John Doe</h2>
        <p class="profile-email">john.doe@example.com</p>
        <button class="btn btn-round btn-d">Change Photo</button>
      </div>

      <div class="profile-tabs">
        <div class="profile-tab active" onclick="showTab('personal')">Personal Info</div>
        <div class="profile-tab" onclick="showTab('addresses')">Addresses</div>
        <div class="profile-tab" onclick="showTab('orders')">Order History</div>
      </div>

      <div class="tab-content">
        <div id="personal-tab" class="tab-panel">
          <div class="section-header">
            <h3 class="section-title">Personal Information</h3>
            <button class="btn-edit" onclick="toggleEdit('personal')"><i class="fa fa-edit"></i> Edit</button>
          </div>
          <form id="personal-form">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="firstName">First Name</label>
                  <input type="text" class="form-control" id="firstName" value="John" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="lastName">Last Name</label>
                  <input type="text" class="form-control" id="lastName" value="Doe" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email">Email Address</label>
                  <input type="email" class="form-control" id="email" value="john.doe@example.com" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="phone">Phone Number</label>
                  <input type="tel" class="form-control" id="phone" value="+1 234 567 8900" readonly>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="birthdate">Date of Birth</label>
              <input type="date" class="form-control" id="birthdate" value="1990-05-15" readonly>
            </div>
            <div class="form-group hidden" id="personal-actions">
              <button type="button" class="btn btn-round btn-d" onclick="savePersonalInfo()">Save Changes</button>
              <button type="button" class="btn btn-round btn-outline" onclick="cancelEdit('personal')" style="margin-left: 10px;">Cancel</button>
            </div>
          </form>
        </div>

        <div id="addresses-tab" class="tab-panel hidden">
          <div class="section-header">
            <h3 class="section-title">Saved Addresses</h3>
            <button class="btn-edit" onclick="addNewAddress()"><i class="fa fa-plus"></i> Add Address</button>
          </div>
          <div class="address-card default">
            <span class="default-badge">Default</span>
            <div class="address-type home">Home</div>
            <h5>John Doe</h5>
            <p>123 Main Street</p>
            <p>Apartment 4B</p>
            <p>New York, NY 10001</p>
            <p>United States</p>
            <div class="address-actions">
              <a href="#" class="btn-sm btn-primary">Edit</a>
              <a href="#" class="btn-sm btn-outline">Remove Default</a>
              <a href="#" class="btn-sm btn-danger">Delete</a>
            </div>
          </div>
          <div class="address-card">
            <div class="address-type work">Work</div>
            <h5>John Doe</h5>
            <p>456 Business Ave</p>
            <p>Suite 200</p>
            <p>New York, NY 10002</p>
            <p>United States</p>
            <div class="address-actions">
              <a href="#" class="btn-sm btn-primary">Edit</a>
              <a href="#" class="btn-sm btn-success" onclick="setDefault(this)">Set as Default</a>
              <a href="#" class="btn-sm btn-danger">Delete</a>
            </div>
          </div>
          <div id="new-address-form" class="address-card hidden" style="border: 2px dashed #007bff;">
            <h5 style="color:#007bff;">Add New Address</h5>
            <form>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Address Type</label>
                    <select class="form-control">
                      <option>Home</option><option>Work</option><option>Other</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="form-control" placeholder="Full name">
                  </div>
                </div>
              </div>
              <div class="form-group"><label>Street Address</label><input type="text" class="form-control" placeholder="Street address"></div>
              <div class="form-group"><label>Apartment/Suite</label><input type="text" class="form-control" placeholder="Apartment, suite, etc."></div>
              <div class="row">
                <div class="col-md-6"><div class="form-group"><label>City</label><input type="text" class="form-control" placeholder="City"></div></div>
                <div class="col-md-6"><div class="form-group"><label>Zip Code</label><input type="text" class="form-control" placeholder="Zip code"></div></div>
              </div>
              <div class="form-group"><label>Country</label>
                <select class="form-control"><option>United States</option><option>Canada</option><option>United Kingdom</option><option>Other</option></select>
              </div>
              <div class="form-group"><label><input type="checkbox"> Set as default address</label></div>
              <div class="address-actions">
                <button type="button" class="btn-sm btn-success" onclick="saveNewAddress()">Save Address</button>
                <button type="button" class="btn-sm btn-outline" onclick="cancelNewAddress()">Cancel</button>
              </div>
            </form>
          </div>
        </div>

        <div id="orders-tab" class="tab-panel hidden">
          <div class="section-header"><h3 class="section-title">Order History</h3></div>
          <div class="order-history-item">
            <div class="order-header"><div class="order-id">Order #ORD-2024-001</div><div class="order-status status-completed">Completed</div></div>
            <p><strong>Date:</strong> March 15, 2024</p><p><strong>Items:</strong> 3 items</p><p><strong>Total:</strong> £89.99</p><p><strong>Delivered to:</strong> Home Address</p>
          </div>
          <div class="order-history-item">
            <div class="order-header"><div class="order-id">Order #ORD-2024-002</div><div class="order-status status-shipped">Shipped</div></div>
            <p><strong>Date:</strong> March 20, 2024</p><p><strong>Items:</strong> 1 item</p><p><strong>Total:</strong> £45.50</p><p><strong>Shipping to:</strong> Work Address</p>
          </div>
          <div class="order-history-item">
            <div class="order-header"><div class="order-id">Order #ORD-2024-003</div><div class="order-status status-pending">Processing</div></div>
            <p><strong>Date:</strong> March 22, 2024</p><p><strong>Items:</strong> 2 items</p><p><strong>Total:</strong> £67.25</p><p><strong>Shipping to:</strong> Home Address</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
function showTab(tabName){document.querySelectorAll('.tab-panel').forEach(p=>p.classList.add('hidden'));document.querySelectorAll('.profile-tab').forEach(t=>t.classList.remove('active'));document.getElementById(tabName+'-tab').classList.remove('hidden');event.target.classList.add('active');}
function toggleEdit(section){const form=document.getElementById(section+'-form');const inputs=form.querySelectorAll('input');const actions=document.getElementById(section+'-actions');const editBtn=event.target;if(inputs[0].readOnly){inputs.forEach(i=>i.readOnly=false);actions.classList.remove('hidden');editBtn.innerHTML='<i class="fa fa-times"></i> Cancel';}else{inputs.forEach(i=>i.readOnly=true);actions.classList.add('hidden');editBtn.innerHTML='<i class="fa fa-edit"></i> Edit';}}
function savePersonalInfo(){alert('Personal information saved successfully!');cancelEdit('personal');}
function cancelEdit(section){const form=document.getElementById(section+'-form');const inputs=form.querySelectorAll('input');const actions=document.getElementById(section+'-actions');const editBtn=form.parentElement.querySelector('.btn-edit');inputs.forEach(i=>i.readOnly=true);actions.classList.add('hidden');editBtn.innerHTML='<i class="fa fa-edit"></i> Edit';}
function addNewAddress(){const form=document.getElementById('new-address-form');form.classList.remove('hidden');form.scrollIntoView({behavior:'smooth'});}
function saveNewAddress(){alert('New address saved successfully!');document.getElementById('new-address-form').classList.add('hidden');}
function cancelNewAddress(){document.getElementById('new-address-form').classList.add('hidden');}
function setDefault(button){const current=document.querySelector('.address-card.default');if(current){current.classList.remove('default');current.querySelector('.default-badge').remove();const defaultBtn=current.querySelector('.btn-success');if(defaultBtn){defaultBtn.textContent='Set as Default';defaultBtn.classList.remove('btn-outline');defaultBtn.classList.add('btn-success');}}const card=button.closest('.address-card');card.classList.add('default');const badge=document.createElement('span');badge.className='default-badge';badge.textContent='Default';card.insertBefore(badge,card.firstChild);button.textContent='Remove Default';button.classList.remove('btn-success');button.classList.add('btn-outline');alert('Default address updated successfully!');}
</script>
</body>
</html>
