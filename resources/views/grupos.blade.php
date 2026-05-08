@extends('layouts.app')

@section('content')
    <div class="row mb-5 align-items-center">
        <div class="col-md-7">
            <h2 class="fw-bold text-purple mb-2">Comunidades Vibely</h2>
            <p class="text-muted lead">Únete a grupos con tus mismos gustos musicales.</p>
        </div>
        <div class="col-md-5 text-md-end">
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="showCreateModal()">
                <i class="fa-solid fa-plus me-2"></i> Crear nuevo grupo
            </button>
        </div>
    </div>

    <!-- ALERTA -->
    <div id="joinSuccess" class="alert alert-success d-none">
        Acción realizada correctamente ✅
    </div>

    <!-- CATEGORÍAS -->
    <div class="mb-4">
        <div class="d-flex gap-2 overflow-auto pb-2">
            <button class="btn btn-purple filter-btn active" data-category="all">Todos</button>
            <button class="btn btn-outline-purple filter-btn" data-category="pop">Pop</button>
            <button class="btn btn-outline-purple filter-btn" data-category="rock">Rock</button>
            <button class="btn btn-outline-purple filter-btn" data-category="techno">Techno</button>
            <button class="btn btn-outline-purple filter-btn" data-category="indie">Indie</button>
            <button class="btn btn-outline-purple filter-btn" data-category="jazz">Jazz</button>
        </div>
    </div>

    @php
        $groups = \App\Models\Group::all();
        $joinedGroups = Auth::user()->groups()->pluck('groups.id')->toArray();
    @endphp

    <div class="row g-4" id="groupsContainer">
        @foreach($groups as $g)
            @php
                $isJoined = in_array($g->id, $joinedGroups);
                $isOwner = $g->user_id == Auth::id();
            @endphp

            <div class="col-md-6 col-lg-4 group-card-item" data-category="{{ $g->category }}">

                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">

                    <div style="height:100px;background:{{ $g->color ?? 'var(--vibely-gradient)' }};" class="position-relative">
                        @if($isOwner)
                            <div class="position-absolute top-0 end-0 p-2 d-flex gap-2">
                                <button class="btn btn-sm btn-light rounded-circle"
                                    onclick="showEditModal({{ $g->id }}, '{{ addslashes($g->name) }}', '{{ $g->category }}', '{{ addslashes($g->description) }}')">
                                    <i class="fa-solid fa-pen text-purple"></i>
                                </button>

                                <button class="btn btn-sm btn-danger rounded-circle"
                                    onclick="deleteGroup({{ $g->id }})">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="card-body p-4 position-relative">

                        <div class="bg-white rounded-3 shadow-sm position-absolute"
                            style="width:50px;height:50px;top:-25px;left:20px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa-solid {{ $g->icon ?? 'fa-users' }} text-purple"></i>
                        </div>

                        <div class="pt-3">
                            <h5 class="fw-bold">{{ $g->name }}</h5>
                            <p class="text-muted small text-truncate-2">{{ $g->description }}</p>

                            <div class="d-flex justify-content-between border-top pt-3">

                                <span class="small members-count" data-id="{{ $g->id }}">
                                    {{ $g->members_count }} miembros
                                </span>

                                <button
                                    class="btn btn-sm {{ $isJoined ? 'btn-purple' : 'btn-outline-purple' }}"
                                    data-joined="{{ $isJoined ? 1 : 0 }}"
                                    onclick="handleJoin(this, {{ $g->id }}, '{{ addslashes($g->name) }}', {{ $isOwner ? 'true' : 'false' }})">
                                    {{ $isJoined ? 'Siguiendo' : 'Unirse' }}
                                </button>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    <!-- EMPTY STATE -->
    <div id="emptyState" class="text-center py-5 d-none">

        <div style="width:90px;height:90px;margin:0 auto;display:flex;align-items:center;justify-content:center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" style="color: #6f42c1;"><path fill="currentColor" d="m19.775 22.575l-4.2-4.175H17q0 .65-.475 1.125T15.4 20H3q-.825 0-1.412-.587T1 18v-.8q0-.85.438-1.562T2.6 14.55q1.55-.775 3.15-1.162T9 13q.3 0 .613.013t.612.037L9.175 12H9q-1.65 0-2.825-1.175T5 8v-.175L1.375 4.2q-.3-.3-.3-.712t.3-.713t.713-.3t.712.3l18.4 18.4q.3.3.3.7t-.3.7t-.712.3t-.713-.3M16.65 13.15q1.275.15 2.4.513t2.1.887q.9.5 1.375 1.112T23 17v3h-.125l-4-4q-.225-.825-.788-1.562T16.65 13.15m-2.6-1.975q.475-.7.713-1.5T15 8q0-1.05-.362-2.025T13.6 4.2q.35-.125.7-.162T15 4q1.65 0 2.825 1.175T19 8t-1.237 2.825T14.875 12zm-1.45-1.45L7.275 4.4q.4-.2.825-.3T9 4q1.65 0 2.825 1.175T13 8q0 .475-.1.9t-.3.825"/></svg>
        </div>

        <h5 class="text-muted mt-3">No hay grupos en esta categoría</h5>

        <button class="btn btn-sm btn-purple mt-3" onclick="openCreateFromEmpty()">
            Crear un grupo nuevo
        </button>
    </div>

    <!-- MODAL CREAR -->
    <div class="modal fade" id="groupModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <h5 id="modalTitle">Crear grupo</h5>

                <form id="groupForm" method="POST" action="{{ route('groups.store') }}">
                    @csrf
                    <input type="hidden" id="editGroupId" name="group_id">

                    <input type="text" id="groupName" name="name" class="form-control mb-2" placeholder="Nombre" required>

                    <select id="groupCategory" name="category" class="form-control mb-2">
                        <option value="pop">Pop</option>
                        <option value="rock">Rock</option>
                        <option value="techno">Techno</option>
                        <option value="indie">Indie</option>
                        <option value="jazz">Jazz</option>
                    </select>

                    <textarea id="groupDesc" name="description" class="form-control mb-2"></textarea>

                    <button class="btn btn-primary w-100">Guardar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL JOIN -->
    <div class="modal fade" id="joinConfirmModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 text-center">
                <h5 id="joinModalText">¿Quieres unirte a este grupo?</h5>
                <div class="mt-3 d-flex gap-2 justify-content-center">
                    <button class="btn btn-success" id="confirmJoinBtn">Aceptar</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL LEAVE -->
    <div class="modal fade" id="leaveConfirmModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 text-center">
                <h5 id="leaveModalText">¿Quieres dejar de seguir este grupo?</h5>
                <div class="mt-3 d-flex gap-2 justify-content-center">
                    <button class="btn btn-danger" id="confirmLeaveBtn">Aceptar</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const joinModal = new bootstrap.Modal(document.getElementById('joinConfirmModal'));
    const leaveModal = new bootstrap.Modal(document.getElementById('leaveConfirmModal'));

    let selectedButton = null;
    let selectedGroupId = null;
    let leaveButton = null;
    let leaveGroupId = null;

    window.showCreateModal = function () {
        new bootstrap.Modal(document.getElementById('groupModal')).show();
    };

    window.showEditModal = function (id, name, category, desc) {
        document.getElementById('modalTitle').innerText = 'Editar grupo';
        document.getElementById('editGroupId').value = id;
        document.getElementById('groupName').value = name;
        document.getElementById('groupCategory').value = category;
        document.getElementById('groupDesc').value = desc;
        new bootstrap.Modal(document.getElementById('groupModal')).show();
    };

    window.handleJoin = function (button, groupId, groupName, isOwner) {

        if (isOwner) return;

        const isJoined = button.dataset.joined == "1";

        if (isJoined) {
            leaveButton = button;
            leaveGroupId = groupId;
            document.getElementById('leaveModalText').innerText = `¿Quieres dejar de seguir ${groupName}?`;
            leaveModal.show();
            return;
        }

        selectedButton = button;
        selectedGroupId = groupId;

        document.getElementById('joinModalText').innerText = `¿Quieres unirte a ${groupName}?`;
        joinModal.show();
    };

    document.getElementById('confirmJoinBtn').addEventListener('click', function () {
        selectedButton.classList.remove('btn-outline-purple');
        selectedButton.classList.add('btn-purple');
        selectedButton.innerText = 'Siguiendo';
        selectedButton.dataset.joined = 1;
        joinModal.hide();
    });

    document.getElementById('confirmLeaveBtn').addEventListener('click', function () {
        leaveButton.classList.remove('btn-purple');
        leaveButton.classList.add('btn-outline-purple');
        leaveButton.innerText = 'Unirse';
        leaveButton.dataset.joined = 0;
        leaveModal.hide();
    });

});

// FILTRO CORREGIDO
document.querySelectorAll('.filter-btn').forEach(btn => {

    btn.addEventListener('click', function () {

        const category = this.dataset.category;
        const cards = document.querySelectorAll('.group-card-item');
        const emptyState = document.getElementById('emptyState');

        let visible = 0;

        cards.forEach(card => {
            const match = category === 'all' || card.dataset.category === category;
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        emptyState.classList.toggle('d-none', visible !== 0);

        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});

window.openCreateFromEmpty = function () {
    let active = document.querySelector('.filter-btn.active');
    let category = active ? active.dataset.category : 'pop';

    showCreateModal();

    setTimeout(() => {
        document.getElementById('groupCategory').value = category !== 'all' ? category : 'pop';
    }, 100);
};
document.getElementById('groupForm').addEventListener('submit', function (e) {
    const nameInput = document.getElementById('groupName');
    const name = nameInput.value.trim();

    if (name === '') {
        e.preventDefault();

        nameInput.classList.add('is-invalid');

        // mensaje visual simple
        alert('El nombre del grupo no puede estar vacío');

        return false;
    } else {
        nameInput.classList.remove('is-invalid');
    }
});
window.deleteGroup = function (id) {

    if (!confirm('¿Seguro que quieres eliminar este grupo?')) return;

    fetch(`/grupos/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Error');
        return res.json();
    })
    .then(data => {

        if (data.status === 'deleted') {

            const card = document.querySelector(`[onclick*="deleteGroup(${id})"]`)
                ?.closest('.group-card-item');

            if (card) {
                card.remove();
            }

            // opcional feedback
            const alert = document.getElementById('joinSuccess');
            alert.classList.remove('d-none');
            alert.innerText = 'Grupo eliminado correctamente';

            setTimeout(() => {
                alert.classList.add('d-none');
            }, 3000);
        }
    })
    .catch(() => {
        alert('No se pudo eliminar el grupo');
    });
};
</script>

<style>
.text-truncate-2{
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
}

.btn-purple{
    background:#6f42c1;
    color:#fff;
}

.filter-btn.active{
    background:#6f42c1 !important;
    color:#fff !important;
    border:1px solid #6f42c1 !important;
}
.is-invalid{
    border: 2px solid red !important;
}
</style>

@endsection