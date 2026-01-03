<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { router } from "@inertiajs/svelte";

    let { event, checkins, flash } = $props();

    let qr_token = $state("");
    let processing = $state(false);

    function submit() {
        if (!qr_token.trim()) return;
        processing = true;
        router.post(
            route("events.checkins.store", event.id),
            { qr_token: qr_token.trim() },
            {
                preserveScroll: true,
                onSuccess: () => (qr_token = ""),
                onFinish: () => (processing = false),
            },
        );
    }
</script>

<AdminLayout title="Check-in">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Check-in</h1>
                <p class="text-sm text-zinc-400">
                    Evento: <span class="text-white">{event.title}</span>
                </p>
            </div>

            <a
                class="text-sm text-zinc-300 hover:text-white underline"
                href={route("events.checkins.export", event.id)}
            >
                Esporta CSV
            </a>
        </div>

        <div class="rounded-md border border-zinc-800 bg-zinc-900/50 p-4 space-y-3">
            <div class="text-sm text-zinc-300">
                Inserisci il token QR (scanner in arrivo, intanto funziona anche incollando).
            </div>

            <div class="flex gap-2">
                <input
                    bind:value={qr_token}
                    placeholder="qr_token..."
                    class="flex-1 bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white font-mono text-xs"
                    onkeydown={(e) => e.key === "Enter" && submit()}
                />
                <button
                    onclick={submit}
                    disabled={processing}
                    class="bg-white text-black px-4 py-2 rounded-md hover:bg-gray-200 transition text-sm font-medium disabled:opacity-50"
                >
                    Check-in
                </button>
            </div>

            {#if flash?.error}
                <div class="text-sm text-red-400">{flash.error}</div>
            {/if}
            {#if flash?.success}
                <div class="text-sm text-green-400">{flash.success}</div>
            {/if}
        </div>

        <div class="rounded-md border border-zinc-800 bg-zinc-900/50">
            <div class="p-4 border-b border-zinc-800 flex items-center justify-between">
                <h2 class="text-white font-semibold">Ultimi check-in</h2>
                <div class="text-xs text-zinc-400">
                    Totale: {checkins.total}
                </div>
            </div>

            <div class="w-full overflow-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-zinc-400 uppercase border-b border-zinc-800">
                        <tr>
                            <th class="px-6 py-3">Quando</th>
                            <th class="px-6 py-3">Socio</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Operatore</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        {#each checkins.data as c (c.id)}
                            <tr class="hover:bg-zinc-900/80 transition">
                                <td class="px-6 py-3 text-zinc-300">
                                    {new Date(c.checked_in_at).toLocaleString("it-IT")}
                                </td>
                                <td class="px-6 py-3 text-white">
                                    {c.membership?.user?.name || "-"}
                                </td>
                                <td class="px-6 py-3 text-zinc-300">
                                    {c.membership?.user?.email || "-"}
                                </td>
                                <td class="px-6 py-3 text-zinc-300">
                                    {c.checked_in_by?.name || "-"}
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</AdminLayout>


