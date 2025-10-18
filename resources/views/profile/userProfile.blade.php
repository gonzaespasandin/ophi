<x-layouts.main>
    <x-slot:title>{{ $user->name }}</x-slot:title>
    
    <section>
        <div>
            <h2>{{ $user->name }}</h2>
            <span>{{ $user->email }}</span>
        </div>
        <div>
            <ul>
                <h3>Perfil médico</h3>
                <span>{{ $medicalProfile->title }}</span>
            </ul>
        </div>
    </section>


</x-layouts.main>