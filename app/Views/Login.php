<?= $this->extend('Base') ?>

<?= $this->section('Body') ?>
<div class="container">
    <div class="card p-4 m-3">
        <img class="img" src="Icons\Whitewall-LOGO-pos.png">
        <form>
            <div class="form-group">
                <label for="InputEmail">Email address</label>
                <input type="email" class="form-control" id="InputEmail" aria-describedby="emailHelp">
                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
                <label for="InputPassword">Password</label>
                <input type="password" class="form-control" id="InputPassword">
            </div>
        </form>
        <button class="btn btn-primary" onclick="login();">Submit</button>
        <div class="row p-2">
            <div class="col-3">
                <div id="g_id_onload" data-client_id="437362021062-fserfra8i9g6kicf00jvefvhbpbf845l.apps.googleusercontent.com" data-context="signin" data-ux_mode="popup" data-callback="login" data-auto_prompt="false">
                </div>
                <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline" data-text="signin_with" data-size="large" data-logo_alignment="left">
                </div>
            </div>
            <div class="col-4">
                <p style="cursor: pointer;" data-mdb-toggle="modal" data-mdb-target="#resetModal"><span class="badge badge-primary">Forgot Username or Password</span></p>
            </div>
            <div class="col-5">

            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">...</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Reset</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>