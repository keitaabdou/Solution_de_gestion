<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Utilisateur extends Component
{
    use WithPagination;
    protected $paginationTheme = "bootstrap";
    public $isBtnAddClicked = false;

    public $newUser = [];

    protected $rules = [
        'newUser.nom' => 'required',
        'newUser.prenom' => 'required',
        'newUser.email' => 'required|email|unique:users,email',
        'newUser.telephone1' => 'required|numeric|unique:users,telephone1',
        'newUser.pieceIdentite' => 'required',
        'newUser.sexe' => 'required',
        'newUser.numeroPieceIdentite' => 'required|unique:users,numeroPieceIdentite'
    ];

    public function render()
    {

        return view('livewire.utilisateurs', [
            "users" => User::latest()->paginate(10)
        ])
            ->extends('layouts.master')
            ->section('contenu');
    }

    public function gotoAddUser(){
        $this->isBtnAddClicked = true;
    }
    public function goToListUser(){
        $this->isBtnAddClicked =  false;
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
