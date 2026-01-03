<script>
    import { page } from "@inertiajs/svelte";
    import { Link } from "@inertiajs/svelte";
    import QRCode from "qrcode";

    let { year, membership } = $props();
    let qrDataUrl = $state(null);
    let token = $derived(membership?.qr_token);

    async function generate() {
        if (!token) return;
        qrDataUrl = await QRCode.toDataURL(token, {
            width: 260,
            margin: 2,
            color: { dark: "#000000", light: "#FFFFFF" },
        });
    }

    $effect(() => {
        generate();
    });
</script>

<svelte:head>
    <title>Tessera {year}</title>
</svelte:head>

<div class="min-h-screen bg-background text-foreground p-6">
    <div class="max-w-md mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Tessera socio {year}</h1>
            <Link
                href="/logout"
                method="post"
                as="button"
                class="text-sm text-muted-foreground hover:text-foreground"
                >Logout</Link
            >
        </div>

        <div class="rounded-lg border bg-card p-6 space-y-4">
            <div class="text-sm text-muted-foreground">
                Mostra questo QR all’ingresso degli eventi.
            </div>

            {#if membership}
                <div class="flex justify-center">
                    {#if qrDataUrl}
                        <img
                            src={qrDataUrl}
                            alt="QR Tessera"
                            class="rounded bg-white p-2"
                        />
                    {:else}
                        <div class="text-sm text-muted-foreground">
                            Generazione QR...
                        </div>
                    {/if}
                </div>

                <div class="text-xs text-muted-foreground break-all">
                    Token: {membership.qr_token}
                </div>
            {:else}
                <div class="text-sm text-red-400">
                    Nessuna tessera attiva per {year}. Contatta la segreteria.
                </div>
            {/if}
        </div>

        <div class="text-center text-sm text-muted-foreground">
            Torna al
            <Link href="/" class="underline hover:text-foreground">sito</Link>
        </div>
    </div>
</div>


