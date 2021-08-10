

<div wire:ignore.self>

    @if($currentPage == PAGECREATEFORM)
        @include("livewire.create")
    @endif
    @if($currentPage == PAGEEDITFORM)
        @include("livewire.edit")
    @endif
    @if($currentPage == PAGELIST)
        @include("livewire.liste")
    @endif

</div>

<script>

    window.addEventListener("showSuccessMessage", event=>{
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            toast: true,
            title: event.detail.message || "Opération effectuée avec succès!",
            showConfirmButton: false,
            timer: 4000
            }
        )
    })
</script>

<script>
        window.addEventListener("showConfirmMessage", event=>{
            Swal.fire({
                title: event.detail.message.title,
                text: event.detail.message.text,
                icon: event.detail.message.type,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Contuner',
                cancelButtonText: 'Annuler'
                }).then((result) => {
                if (result.isConfirmed) {
                    if(event.detail.message.data){
                        @this.deletUser(event.details.message.data.user_id)
                    }
                @this.resetPassword()

            }
            })
        })

</script>
