<?php

namespace App\Http\Livewire;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
        Carbon::setLocale("fr");

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


    }
    public function updateRoleAndPermissions(){
        DB::table("user_role")->where("user_id", $this->editUser["id"])->delete();
        DB::table("user_permission")->where("user_id", $this->editUser["id"])->delete();

        foreach($this->rolePermissions["roles"] as $role){
           if($role["active"]){
            User::find($this->editUser["id"])->roles()->attach($role["role_id"]);
           }
        }

        foreach($this->rolePermissions["permissions"] as $permission){
            if($permission["active"]){
                User::find($this->editUser["id"])->permissions()->attach($permission["permission_id"]);
            }
        }
        $this->dispatchBrowserEvent("showSuccessMessage", ["message" => "Roles et permissions mise  a jour  avec succ??s!"]);
    }

    public function goToListUser(){
        $this->currentPage = PAGELIST;
        $this->editUser = [];
    }

    public function addUser(){


        // V??rifier que les informations envoy??es par le formualire sont correctes
        $validationAttributes= $this->validate();


        $validationAttributes["newUser"]["password"] = "password";
        //dump($validationAttributes);
        //Ajouter un nouvelle utilisateur
        User::create($validationAttributes["newUser"]);

        $this->newUser = [];

        $this->dispatchBrowserEvent("showSuccessMessage", ["message" => "Utilisateur cr??e avec succ??s!"]);
    }

    //erreur ici
    public function updateUser(){

       // V??rifier que les informations envoy??es par le formualire sont correctes
       $validationAttributes= $this->validate();


       User::find($this->editUser["id"])->update($validationAttributes["editUser"]);

       $this->dispatchBrowserEvent("showSuccessMessage", ["message" => "Utilisateur mise ?? jour avec succ??s!"]);

    }

    public function confirmPwdReset(){
        $this->dispatchBrowserEvent("showConfirmMessage", ["message" => [
            "text" => "Vous ??tes sur le point de r??initialis?? le mots de passe de cet utilisateurs. Voulez-vous continnuer",
            "title" => "Etes vous s??r de continer ?",
            "type" => "warning"
        ]]);
    }

    public function resetPassword(){

        User::find($this->editUser["id"])->update(["password"=> Hash::make(DEFAULTPASSWORD)]);
        $this->dispatchBrowserEvent("showSuccessMessage", ["message" => "Mot de passe utilisateur r??intialis?? avce succ??s!"]);
    }
    //erreur ici fin

    public function confirmDelete($name, $id){
        $this->dispatchBrowserEvent("showConfirmMessage", ["message" => [
            "text" => "Vous ??tes sur le point de supprimer $name de la lite des utilisateurs. Voulez-vous continnuer",
            "title" => "Etes vous s??r de continer ?",
            "type" => "warning",
            "data" => [
                "user_id" => $id
            ]
        ]]);

    }

    public function deleteUser($id){
        User::destroy($id);
        $this->dispatchBrowserEvent("showSuccessMessage", ["message" => "Utilisateur supprim?? avec succ??s!"]);

    }
}
