<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { Button } from "@/lib/components/ui/button";
    import * as Card from "@/lib/components/ui/card";
    import * as Table from "@/lib/components/ui/table";
    import { Badge } from "@/lib/components/ui/badge";
    import { Link } from "@inertiajs/svelte";
    import PlusIcon from "@tabler/icons-svelte/icons/plus";
    import EyeIcon from "@tabler/icons-svelte/icons/eye";
    import TrashIcon from "@tabler/icons-svelte/icons/trash";
    import ImageIcon from "@tabler/icons-svelte/icons/photo";
    import PaperclipIcon from "@tabler/icons-svelte/icons/paperclip";
    import { router } from "@inertiajs/svelte";
    import * as Dialog from "@/lib/components/ui/dialog";

    let { broadcasts } = $props();

    let confirmDeleteOpen = $state(false);
    let broadcastToDelete = $state(null);

    function handleDelete(broadcast) {
        broadcastToDelete = broadcast;
        confirmDeleteOpen = true;
    }

    function confirmDelete() {
        if (!broadcastToDelete) return;
        router.delete(`/admin/broadcasts/${broadcastToDelete.id}`, {
            onSuccess: () => {
                confirmDeleteOpen = false;
                broadcastToDelete = null;
            },
        });
    }

    function formatDate(dateString) {
        if (!dateString) return "-";
        return new Date(dateString).toLocaleString("it-IT", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    }
</script>

<AdminLayout title="Notifiche Broadcast">
    <div class="space-y-6">
        <div
            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Notifiche Broadcast
                </h1>
                <p class="text-sm text-muted-foreground">
                    Invia comunicazioni a tutti i soci attivi della Pro Loco.
                </p>
            </div>
            <Button href="/admin/broadcasts/create" class="shadow-sm">
                <PlusIcon class="mr-2 size-4" />
                Nuova Notifica
            </Button>
        </div>

        <Card.Root>
            <Card.Content class="p-0">
                {#if broadcasts?.data?.length}
                    <Table.Root>
                        <Table.Header>
                            <Table.Row>
                                <Table.Head>Titolo</Table.Head>
                                <Table.Head class="w-24">Media</Table.Head>
                                <Table.Head class="w-40">Inviata il</Table.Head>
                                <Table.Head class="w-40">Creata il</Table.Head>
                                <Table.Head class="w-24"></Table.Head>
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {#each broadcasts.data as broadcast}
                                <Table.Row>
                                    <Table.Cell>
                                        <div class="font-medium">
                                            {broadcast.title}
                                        </div>
                                        {#if broadcast.created_by}
                                            <div
                                                class="text-xs text-muted-foreground"
                                            >
                                                di {broadcast.created_by.name}
                                            </div>
                                        {/if}
                                    </Table.Cell>
                                    <Table.Cell>
                                        <div class="flex items-center gap-1">
                                            {#if broadcast.featured_image_url}
                                                <ImageIcon
                                                    class="size-4 text-muted-foreground"
                                                />
                                            {/if}
                                            {#if broadcast.attachment_url}
                                                <PaperclipIcon
                                                    class="size-4 text-muted-foreground"
                                                />
                                            {/if}
                                        </div>
                                    </Table.Cell>
                                    <Table.Cell>
                                        {#if broadcast.sent_at}
                                            <Badge variant="default"
                                                >Inviata</Badge
                                            >
                                            <div
                                                class="mt-1 text-xs text-muted-foreground"
                                            >
                                                {formatDate(broadcast.sent_at)}
                                            </div>
                                        {:else}
                                            <Badge variant="secondary"
                                                >Bozza</Badge
                                            >
                                        {/if}
                                    </Table.Cell>
                                    <Table.Cell class="text-muted-foreground">
                                        {formatDate(broadcast.created_at)}
                                    </Table.Cell>
                                    <Table.Cell>
                                        <div class="flex items-center gap-1">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                href={`/admin/broadcasts/${broadcast.id}`}
                                            >
                                                <EyeIcon class="size-4" />
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                onclick={() =>
                                                    handleDelete(broadcast)}
                                            >
                                                <TrashIcon
                                                    class="size-4 text-destructive"
                                                />
                                            </Button>
                                        </div>
                                    </Table.Cell>
                                </Table.Row>
                            {/each}
                        </Table.Body>
                    </Table.Root>
                {:else}
                    <div
                        class="flex flex-col items-center justify-center py-12 text-center"
                    >
                        <p class="mb-2 text-sm font-medium">
                            Nessuna notifica broadcast
                        </p>
                        <p class="text-sm text-muted-foreground">
                            Crea la prima notifica per raggiungere tutti i soci
                            attivi.
                        </p>
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog.Root bind:open={confirmDeleteOpen}>
        <Dialog.Content>
            <Dialog.Header>
                <Dialog.Title>Elimina Notifica</Dialog.Title>
                <Dialog.Description>
                    Sei sicuro di voler eliminare la notifica "<strong
                        >{broadcastToDelete?.title}</strong
                    >"? Questa azione non può essere annullata.
                </Dialog.Description>
            </Dialog.Header>
            <Dialog.Footer>
                <Button
                    variant="outline"
                    onclick={() => (confirmDeleteOpen = false)}
                >
                    Annulla
                </Button>
                <Button variant="destructive" onclick={confirmDelete}>
                    Elimina
                </Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>
</AdminLayout>
