<?php

namespace App\Http\Livewire;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Utilisateur extends Component
{
    use WithPagination;
    protected $paginationTheme = "bootstrap";

    public $currentPage = PAGELIST;

    public $newUser = [];
    public $editUser = [];

    public $rolePermissions = [];

    public function render()
    {

        return view('livewire.utilisateurs', [
            "users" => User::latest()->paginate(10)
        ])
            ->extends('layouts.master')
            ->section('contenu');
    }

    public function rules(){
        if($this->currentPage == PAGEEDITFORM){
            return [
                'editUser.nom' => 'required',
                'editUser.prenom' => 'required',
                'editUser.email' => ['required', 'email', Rule::unique("users", "email")->ignore($this->editUser['id'])],
                'editUser.telephone1' => ['required', 'numeric', Rule::unique("users", "email")->ignore($this->editUser['id'])],
                'editUser.pieceIdentite' => 'required',
                'editUser.sexe' => 'required',
                'editUser.numeroPieceIdentite' => ['required', Rule::unique("users", "numeroPieceIdentite")->ignore($this->editUser['id'])],
            ];


        }

        return [
            'newUser.nom' => 'required',
            'newUser.prenom' => 'required',
            'newUser.email' => 'required|email|unique:users,email',
            'newUser.telephone1' => 'required|numeric|unique:users,telephone1',
            'newUser.pieceIdentite' => 'required',
            'newUser.sexe' => 'required',
            'newUser.numeroPieceIdentite' => 'required|unique:users,numeroPieceIdentite'
        ];
    }

    public function gotoAddUser(){
        $this->currentPage = PAGECREATEFORM;
    }
    public function goToEditUser($id){
        $this->editUser = User::find($id)->toArray();
        $this->currentPage = PAGEEDITFORM;

        $this->populateRolePermissions();
    }

    public function populateRolePermissions(){
        $this->rolePermissions["roles"] = [];
        $this->rolePermissions["permissions"] = [];

        $mapForCB = function($value){
            return $value["id"];
        };
        $rolesIds = array_map($mapForCB, User::find($this->editUser["id"])->roles->toArray());
        $permissionIds = array_map($mapForCB, User::find($this->editUser["id"])->permissions->toArray());

        foreach(Role::all() as $role){
            if(in_array($role->id, $rolesIds)){
                array_push($this->rolePermissions["roles"], ["role_id"=>$role->id, "role_nom"=>$role->nom, "active"=>true]);
            }else{
                array_push($this->rolePermissions["roles"], ["role_id"=>$role->id, "role_nom"=>$role->nom, "active"=>false]);

            }
        }
        foreach(Permission::all() as $permission){
            if(in_array($permission->id, $permissionIds)){
                array_push($this->rolePermissions["permissions"], ["permission_id"=>$permission->id, "permission_nom"=>$permission->nom, "active"=>true]);
            }else{
                array_push($this->rolePermissions["permissions"], ["permission_id"=>$permission->id, "permission_nom"=>$permission->nom, "active"=>false]);

            }
        }
        //dump($this->rolePermissions);

    }

    public function goToListUser(){
        $this->currentPage = PAGELIST;
        $this->editUser = [];
    }

    public function addUser(){


        // Vérifier que les informations envoyées par le formualire sont correctes
        $validationAttributes= $this->validate();


        $validationAttributes["newUser"]["password"] = "password";
        //dump($validationAttributes);
        //Ajouter un nouvelle utilisateur
        User::create($validationAttributes["newUser"]);

        $this->newUser = [];

        $this->dispatchBrowserEvent("showSuccessMessage", ["message" => "Utilisateur crée avec succès!"]);
    }

    //erreur ici
    public function updateUser(){

       // Vérifier que les informations envoyées par le formualire sont correctes
       $validationAttributes= $this->validate();


       User::find($this->editUser["id"])->update($validationAttributes["editUser"]);

       $this->dispatchBrowserEvent("showSuccessMessage", ["message" => "Utilisateur mise à jour avec succès!"]);

    }

    public function confirmPwdReset(){
        $this->dispatchBrowserEvent("showConfirmMessage", ["message" => [
            "text" => "Vous êtes sur le point de réinitialisé le mots de passe de cet utilisateurs. Voulez-vous continnuer",
            "title" => "Etes vous sûr de continer ?",
            "type" => "warning"
        ]]);
    }

    public function resetPassword(){

        User::find($this->editUser["id"])->update(["password"=> Hash::make(DEFAULTPASSWORD)]);
        $this->dispatchBrowserEvent("showSuccessMessage", ["message" => "Mot de passe utilisateur réintialisé avce succès!"]);
    }
    //erreur ici fin

    public function confirmDelete($name, $id){
        $this->dispatchBrowserEvent("showConfirmMessage", ["message" => [
            "text" => "Vous êtes sur le point de supprimer $name de la lite des utilisateurs. Voulez-vous continnuer",
            "title" => "Etes vous sûr de continer ?",
            "type" => "warning",
            "data" => [
                "user_id" => $id
            ]
        ]]);

    }

    public function deleteUser($id){
        User::destroy($id);
        $this->dispatchBrowserEvent("showSuccessMessage", ["message" => "Utilisateur supprimé avec succès!"]);

    }
}
