<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { router } from "@inertiajs/svelte";

    let { pages, flash } = $props();

    let processing = $state(false);
    let editing = $state(null);

    let form = $state({
        title: "",
        slug: "",
        excerpt: "",
        body: "",
        status: "draft",
    });

    function reset() {
        form = { title: "", slug: "", excerpt: "", body: "", status: "draft" };
        editing = null;
    }

    function edit(page) {
        editing = page;
        form = {
            title: page.title,
            slug: page.slug,
            excerpt: page.excerpt || "",
            body: page.body,
            status: page.status,
        };
    }

    function submit() {
        processing = true;
        if (editing) {
            router.put(route("content-pages.update", editing.id), form, {
                preserveScroll: true,
                onFinish: () => (processing = false),
            });
        } else {
            router.post(route("content-pages.store"), form, {
                preserveScroll: true,
                onSuccess: reset,
                onFinish: () => (processing = false),
            });
        }
    }

    function remove(pageId) {
        if (!confirm("Eliminare questa pagina?")) return;
        router.delete(route("content-pages.destroy", pageId), {
            preserveScroll: true,
        });
    }
</script>

<AdminLayout title="Contenuti">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Contenuti</h1>
                <p class="text-sm text-zinc-400">
                    Pagine pubbliche pubblicabili su `/p/{slug}`.
                </p>
            </div>
            <button
                onclick={reset}
                class="text-sm text-zinc-300 hover:text-white underline"
            >
                Nuova pagina
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-md border border-zinc-800 bg-zinc-900/50">
                <div class="p-4 border-b border-zinc-800">
                    <h2 class="text-white font-semibold">
                        {editing ? "Modifica" : "Crea"} pagina
                    </h2>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1"
                            >Titolo</label
                        >
                        <input
                            bind:value={form.title}
                            class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                        />
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1"
                            >Slug</label
                        >
                        <input
                            bind:value={form.slug}
                            placeholder="lascia vuoto per auto"
                            class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white font-mono text-xs"
                        />
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1"
                            >Excerpt</label
                        >
                        <input
                            bind:value={form.excerpt}
                            class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                        />
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1"
                            >Body</label
                        >
                        <textarea
                            bind:value={form.body}
                            rows="10"
                            class="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                        ></textarea>
                    </div>
                    <div class="flex items-center justify-between">
                        <select
                            bind:value={form.status}
                            class="bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-white focus:outline-none focus:border-white"
                        >
                            <option value="draft">Bozza</option>
                            <option value="published">Pubblicata</option>
                        </select>
                        <button
                            onclick={submit}
                            disabled={processing}
                            class="bg-white text-black px-4 py-2 rounded-md hover:bg-gray-200 transition text-sm font-medium disabled:opacity-50"
                        >
                            {processing ? "Salvataggio..." : "Salva"}
                        </button>
                    </div>

                    {#if flash?.error}
                        <div class="text-sm text-red-400">{flash.error}</div>
                    {/if}
                    {#if flash?.success}
                        <div class="text-sm text-green-400">{flash.success}</div>
                    {/if}
                </div>
            </div>

            <div class="rounded-md border border-zinc-800 bg-zinc-900/50">
                <div class="p-4 border-b border-zinc-800">
                    <h2 class="text-white font-semibold">Pagine</h2>
                </div>
                <div class="divide-y divide-zinc-800">
                    {#each pages.data as p (p.id)}
                        <div class="p-4 flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="text-white font-medium truncate">
                                    {p.title}
                                </div>
                                <div class="text-xs text-zinc-400 font-mono">
                                    /p/{p.slug} • {p.status}
                                </div>
                                {#if p.excerpt}
                                    <div class="text-sm text-zinc-300 mt-1">
                                        {p.excerpt}
                                    </div>
                                {/if}
                            </div>
                            <div class="flex gap-3 shrink-0">
                                <button
                                    onclick={() => edit(p)}
                                    class="text-sm text-zinc-300 hover:text-white underline"
                                >
                                    Modifica
                                </button>
                                <button
                                    onclick={() => remove(p.id)}
                                    class="text-sm text-red-400 hover:text-red-300 underline"
                                >
                                    Elimina
                                </button>
                            </div>
                        </div>
                    {/each}
                </div>
            </div>
        </div>
    </div>
</AdminLayout>


