<div class="row p-4 pt-5">
    <div class="col-md-7">
        <!-- general form elements -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-plus fa-2x"></i> Formulaire de création d'un nouvel utilisateur</h3>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" wire:submit.prevent="addUser()">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Nom</label>
                            <input type="text" wire:model="newUser.nom"  class="form-control @error('newUser.nom') is-invalid @enderror">

                            @error("newUser.nom")
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                          </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Prenom</label>
                            <input type="text" wire:model="newUser.prenom" class="form-control @error('newUser.prenom') is-invalid @enderror">

                            @error("newUser.prenom")
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                          </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Sexe</label>
                    <select class="form-control @error('newUser.sexe') is-invalid @enderror"
                     wire:model="newUser.sexe">
                        <option value="">---------</option>
                        <option value="H">Homme</option>
                        <option value="F">Femme</option>
                    </select>

                    @error("newUser.sexe")
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
              <div class="form-group">
                <label>Adresse e-mail</label>
                <input type="email" wire:model="newUser.email" class="form-control @error('newUser.email') is-invalid @enderror">
                    @error("newUser.email")
                        <span class="text-danger">{{$message}}</span>
                    @enderror
              </div>
              <div class="row">
                <div class="col-8">
                    <div class="form-group">
                        <label>Téléphone 1</label>
                        <input type="text" wire:model="newUser.telephone1" class="form-control @error('newUser.telephone1') is-invalid @enderror">
                    @error("newUser.telephone1")
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                      </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Téléphone 2</label>
                        <input type="text" wire:model="newUser.telephone2" class="form-control">
                      </div>
                </div>
            </div>
            <div class="form-group">
                <label>Piece d'identité</label>
                <select class="form-control @error('newUser.pieceIdentite') is-invalid @enderror"
                wire:model="newUser.pieceIdentite">
                    <option value="">---------</option>
                    <option value="CNI">CNI</option>
                    <option value="PASSPORT">PASSPORT</option>
                    <option value="PERMIS DE CONDUIRE">PERMIS DE CONDUIRE</option>
                </select>

            @error("newUser.pieceIdentite")
                <span class="text-danger">{{$message}}</span>
            @enderror
              </div>
                <div class="form-group">
                    <label>numero de piece d'identite</label>
                    <input type="text" wire:model="newUser.numeroPieceIdentite" class="form-control @error('newUser.numeroPieceIdentite') is-invalid @enderror">

                @error("newUser.numeroPieceIdentite")
                     <span class="text-danger">{{$message}}</span>
                @enderror
                </div>

              <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password"  class="form-control" disabled placeholder="Password">
              </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Enregistrer</button>
              <button type="button" wire:click="goToListUser()" class="btn btn-danger">Returner à la liste des utilisateurs</button>
            </div>
          </form>
        </div>
        <!-- /.card -->
      </div>
</div>

