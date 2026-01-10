<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { router } from "@inertiajs/svelte";
    import { page } from "@inertiajs/svelte";
    import { Button } from "@/lib/components/ui/button";
    import { Input } from "@/lib/components/ui/input";
    import * as Card from "@/lib/components/ui/card";
    import * as Table from "@/lib/components/ui/table";

    import ChevronLeft from "lucide-svelte/icons/chevron-left";
    import Download from "lucide-svelte/icons/download";

    let { event, checkins } = $props();
    let flash = $derived($page.props.flash);

    let qr_code = $state("");
    let processing = $state(false);

    function submit() {
        if (!qr_code.trim()) return;
        processing = true;
        router.post(
            `/admin/events/${event.id}/checkins`,
            { qr_code: qr_code.trim() },
            {
                preserveScroll: true,
                onSuccess: () => (qr_code = ""),
                onFinish: () => (processing = false),
            },
        );
    }
</script>

<AdminLayout
    title="Presenze Evento"
    breadcrumbs={[{ label: "Eventi", href: "/admin/events" }]}
>
    <div class="space-y-6">
        <div
            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <div class="flex items-center gap-4">
                <Button
                    variant="ghost"
                    size="icon"
                    class="h-9 w-9"
                    onclick={() => router.get("/admin/events")}
                >
                    <ChevronLeft class="size-5" />
                </Button>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Presenze Evento
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        {event.title}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    class="shadow-sm"
                    href={`/admin/events/${event.id}/checkins/export`}
                >
                    <Download class="h-4 w-4 mr-2" />
                    Esporta CSV
                </Button>
            </div>
        </div>

        <Card.Root>
            <Card.Header>
                <Card.Title>Registra presenza</Card.Title>
                <Card.Description>
                    Inserisci il codice UUID del socio (tramite scanner o
                    copia-incolla).
                </Card.Description>
            </Card.Header>
            <Card.Content class="space-y-3">
                <div class="flex gap-2">
                    <Input
                        bind:value={qr_code}
                        placeholder="UUID del socio..."
                        class="flex-1 font-mono text-xs"
                        onkeydown={(e) => e.key === "Enter" && submit()}
                    />
                    <Button onclick={submit} disabled={processing}>
                        Check-in
                    </Button>
                </div>

                {#if flash?.error}
                    <div class="text-sm text-destructive">{flash.error}</div>
                {/if}
                {#if flash?.success}
                    <div class="text-sm text-green-600 dark:text-green-400">
                        {flash.success}
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>

        <Card.Root>
            <Card.Header
                class="flex-row items-center justify-between space-y-0"
            >
                <Card.Title class="text-base">Ultimi check-in</Card.Title>
                <div class="text-xs text-muted-foreground">
                    Totale: {checkins.total}
                </div>
            </Card.Header>
            <Card.Content class="p-0">
                <Table.Root>
                    <Table.Header>
                        <Table.Row>
                            <Table.Head>Quando</Table.Head>
                            <Table.Head>Socio</Table.Head>
                            <Table.Head>Email</Table.Head>
                            <Table.Head>Operatore</Table.Head>
                        </Table.Row>
                    </Table.Header>
                    <Table.Body>
                        {#each checkins.data as c (c.id)}
                            <Table.Row>
                                <Table.Cell class="text-muted-foreground">
                                    {new Date(c.checked_in_at).toLocaleString(
                                        "it-IT",
                                    )}
                                </Table.Cell>
                                <Table.Cell class="font-medium">
                                    {c.membership?.user?.name || "-"}
                                </Table.Cell>
                                <Table.Cell class="text-muted-foreground">
                                    {c.membership?.user?.email || "-"}
                                </Table.Cell>
                                <Table.Cell class="text-muted-foreground">
                                    {c.checked_in_by?.name || "-"}
                                </Table.Cell>
                            </Table.Row>
                        {/each}
                    </Table.Body>
                </Table.Root>
            </Card.Content>
        </Card.Root>
    </div>
</AdminLayout>
