<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { Button } from "@/lib/components/ui/button";
    import * as Card from "@/lib/components/ui/card";
    import * as Dialog from "@/lib/components/ui/dialog";
    import * as Popover from "@/lib/components/ui/popover";
    import * as Command from "@/lib/components/ui/command";
    import { cn } from "@/lib/utils";
    import * as Table from "@/lib/components/ui/table";
    import { Input } from "@/lib/components/ui/input";
    import { Label } from "@/lib/components/ui/label";
    import { Badge } from "@/lib/components/ui/badge";
    import { Switch } from "@/lib/components/ui/switch";
    import { router } from "@inertiajs/svelte";
    import PlusIcon from "@tabler/icons-svelte/icons/plus";
    import TrashIcon from "@tabler/icons-svelte/icons/trash";
    import UserIcon from "@tabler/icons-svelte/icons/user";
    import UploadIcon from "@tabler/icons-svelte/icons/upload";
    import FileIcon from "@tabler/icons-svelte/icons/file";
    import CheckIcon from "@tabler/icons-svelte/icons/check";
    import EyeIcon from "@tabler/icons-svelte/icons/eye";
    import EditIcon from "@tabler/icons-svelte/icons/edit";
    import SelectorIcon from "@tabler/icons-svelte/icons/selector";
    import { formatDistanceToNow } from "date-fns";
    import { it } from "date-fns/locale";
    import ArrowLeftIcon from "@tabler/icons-svelte/icons/arrow-left";
    import { toast } from "svelte-sonner";

    let { committee, availableMembers = [] } = $props();

    let addMemberDialogOpen = $state(false);
    let openCombobox = $state(false);

    let selectedMemberId = $state(null);
    let memberRole = $state("");

    // Image Upload State (for committee logo, existing)
    let selectedImageFile = $state(null);
    let imageInputRef = $state(null);

    // Confirmation Dialogs
    let confirmRemovalOpen = $state(false);
    let memberToRemoveId = $state(null);
    let memberToRemoveName = $state("");

    let confirmPostDeletionOpen = $state(false);
    let postToDeleteId = $state(null);

    let confirmImageDeletionOpen = $state(false);

    function handleAddMember() {
        if (!selectedMemberId) return;

        router.post(
            `/admin/committees/${committee.id}/members`,
            {
                user_id: selectedMemberId,
                role: memberRole || null,
            },
            {
                onSuccess: () => {
                    addMemberDialogOpen = false;
                    selectedMemberId = null;
                    memberRole = "";
                },
            },
        );
    }

    function handleRemoveMember(userId, userName) {
        memberToRemoveId = userId;
        memberToRemoveName = userName;
        confirmRemovalOpen = true;
    }

    function confirmRemoveMember() {
        if (!memberToRemoveId) return;
        router.delete(
            `/admin/committees/${committee.id}/members/${memberToRemoveId}`,
            {
                onSuccess: () => {
                    confirmRemovalOpen = false;
                    memberToRemoveId = null;
                    memberToRemoveName = "";
                },
            },
        );
    }

    function handleStatusToggle() {
        const newStatus = committee.status === "active" ? "inactive" : "active";
        router.patch(
            `/admin/committees/${committee.id}`,
            {
                name: committee.name,
                status: newStatus,
            },
            {
                preserveScroll: true,
            },
        );
    }

    function handleImageUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append("image", file);

        router.post(`/admin/committees/${committee.id}/image`, formData, {
            preserveScroll: true,
            onSuccess: () => {
                selectedImageFile = null;
                if (imageInputRef) {
                    imageInputRef.value = "";
                }
            },
        });
    }

    function confirmDeleteImage() {
        router.delete(`/admin/committees/${committee.id}/image`, {
            preserveScroll: true,
            onSuccess: () => {
                confirmImageDeletionOpen = false;
            },
        });
    }

    function handleDeletePost(postId) {
        postToDeleteId = postId;
        confirmPostDeletionOpen = true;
    }

    function confirmDeletePost() {
        if (!postToDeleteId) return;
        router.delete(
            `/admin/committees/${committee.id}/posts/${postToDeleteId}`,
            {
                onSuccess: () => {
                    confirmPostDeletionOpen = false;
                    postToDeleteId = null;
                },
            },
        );
    }
</script>

<AdminLayout
    title={committee.name}
    breadcrumbs={[{ label: "Comitati", href: "/admin/committees" }]}
>
    <div class="@container/main space-y-6">
        <div
            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <div class="flex items-center gap-4">
                <Button
                    variant="ghost"
                    size="icon"
                    class="h-9 w-9"
                    onclick={() => router.get("/admin/committees")}
                >
                    <ArrowLeftIcon class="size-5" />
                </Button>
                <div class="flex items-center gap-6">
                    <!-- Committee Image (Logo) -->
                    <div class="relative group">
                        <div
                            class="size-16 rounded-xl border-2 border-dashed border-muted-foreground/25 bg-muted flex items-center justify-center overflow-hidden transition-all group-hover:border-primary/50"
                        >
                            {#if committee.image_url}
                                <img
                                    src={committee.image_url}
                                    alt={committee.name}
                                    class="h-full w-full object-cover"
                                />
                            {:else}
                                <div class="text-center p-1">
                                    <UserIcon
                                        class="size-5 mx-auto text-muted-foreground/50 mb-0.5"
                                    />
                                    <span
                                        class="text-[8px] text-muted-foreground uppercase font-semibold tracking-wider"
                                        >No Logo</span
                                    >
                                </div>
                            {/if}
                        </div>

                        <!-- Upload/Delete Overlay -->
                        <div
                            class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5"
                        >
                            <Button
                                variant="secondary"
                                size="icon"
                                class="size-7 rounded-full"
                                onclick={() => imageInputRef?.click()}
                                title="Carica logo"
                            >
                                <UploadIcon class="size-3.5" />
                            </Button>
                            {#if committee.image_url}
                                <Button
                                    variant="destructive"
                                    size="icon"
                                    class="size-7 rounded-full"
                                    onclick={() =>
                                        (confirmImageDeletionOpen = true)}
                                    title="Rimuovi logo"
                                >
                                    <TrashIcon class="size-3.5" />
                                </Button>
                            {/if}
                        </div>

                        <input
                            type="file"
                            accept="image/*"
                            class="hidden"
                            bind:this={imageInputRef}
                            onchange={handleImageUpload}
                        />
                    </div>

                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">
                            {committee.name}
                        </h1>
                        <p class="text-sm text-muted-foreground">
                            {committee.description ||
                                "Gestisci membri, cariche e bacheca comunicazioni del comitato."}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <Label
                        for="committee-status"
                        class="text-xs font-medium uppercase tracking-wider text-muted-foreground"
                    >
                        {committee.status === "active" ? "Attivo" : "Inattivo"}
                    </Label>
                    <Switch
                        id="committee-status"
                        checked={committee.status === "active"}
                        onCheckedChange={handleStatusToggle}
                    />
                </div>
                <Badge
                    variant={committee.status === "active"
                        ? "default"
                        : "secondary"}
                >
                    {committee.status === "active" ? "Attivo" : "Inattivo"}
                </Badge>
            </div>
        </div>

        <!-- Members Section -->
        <Card.Root>
            <Card.Header>
                <div class="flex items-center justify-between">
                    <div>
                        <Card.Title>Membri del Comitato</Card.Title>
                        <Card.Description>
                            {committee.members.length}
                            {committee.members.length === 1
                                ? "membro"
                                : "membri"} associati
                        </Card.Description>
                    </div>
                    <Button
                        onclick={() => (addMemberDialogOpen = true)}
                        disabled={availableMembers.length === 0}
                    >
                        <PlusIcon class="mr-2 size-4" />
                        Aggiungi Socio
                    </Button>
                </div>
            </Card.Header>
            <Card.Content>
                {#if committee.members.length === 0}
                    <div
                        class="flex flex-col items-center justify-center py-8 text-center"
                    >
                        <UserIcon class="mb-3 size-12 text-muted-foreground" />
                        <p class="mb-2 text-sm font-medium">Nessun membro</p>
                        <p class="text-sm text-muted-foreground">
                            Aggiungi dei membri a questo comitato.
                        </p>
                    </div>
                {:else}
                    <Table.Root>
                        <Table.Header>
                            <Table.Row>
                                <Table.Head>Nome</Table.Head>
                                <Table.Head>Email</Table.Head>
                                <Table.Head>Ruolo</Table.Head>
                                <Table.Head>Aggiunto il</Table.Head>
                                <Table.Head class="w-12"></Table.Head>
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {#each committee.members as member}
                                <Table.Row>
                                    <Table.Cell class="font-medium"
                                        >{member.name}</Table.Cell
                                    >
                                    <Table.Cell>{member.email}</Table.Cell>
                                    <Table.Cell>
                                        {member.pivot.role || "-"}
                                    </Table.Cell>
                                    <Table.Cell class="text-muted-foreground">
                                        {new Date(
                                            member.pivot.joined_at,
                                        ).toLocaleDateString("it-IT")}
                                    </Table.Cell>
                                    <Table.Cell>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onclick={() =>
                                                handleRemoveMember(
                                                    member.id,
                                                    member.name,
                                                )}
                                        >
                                            <TrashIcon
                                                class="size-4 text-destructive"
                                            />
                                        </Button>
                                    </Table.Cell>
                                </Table.Row>
                            {/each}
                        </Table.Body>
                    </Table.Root>
                {/if}
            </Card.Content>
        </Card.Root>

        <!-- Posts Section (Bulletin Board) -->
        <Card.Root>
            <Card.Header>
                <div class="flex items-center justify-between">
                    <div>
                        <Card.Title>Bacheca</Card.Title>
                        <Card.Description>
                            Post e comunicazioni del comitato
                        </Card.Description>
                    </div>
                    <Button
                        href="/admin/committees/{committee.id}/posts/create"
                    >
                        <PlusIcon class="mr-2 size-4" />
                        Nuovo Post
                    </Button>
                </div>
            </Card.Header>
            <Card.Content>
                {#if committee.posts.length === 0}
                    <div
                        class="flex flex-col items-center justify-center py-8 text-center"
                    >
                        <p class="mb-2 text-sm font-medium">Nessun post</p>
                        <p class="text-sm text-muted-foreground">
                            Pubblica il primo post nella bacheca del comitato.
                        </p>
                    </div>
                {:else}
                    <div
                        class="rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden"
                    >
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-muted/50 border-b">
                                        <th
                                            class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground text-center w-12"
                                            >#</th
                                        >
                                        <th
                                            class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground"
                                            >Post</th
                                        >
                                        <th
                                            class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground w-32 text-center"
                                            >Visualizzazioni</th
                                        >
                                        <th
                                            class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground w-32 text-center"
                                            >Media</th
                                        >
                                        <th
                                            class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground w-28 text-right"
                                            >Azioni</th
                                        >
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    {#each committee.posts as post, i (post.id)}
                                        <tr
                                            class="hover:bg-muted/30 transition-colors group"
                                        >
                                            <td
                                                class="px-4 py-3 text-center text-xs text-muted-foreground font-mono"
                                            >
                                                {i + 1}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="font-medium text-sm"
                                                        >{post.title}</span
                                                    >
                                                    <span
                                                        class="text-[10px] text-muted-foreground mt-0.5"
                                                    >
                                                        {formatDistanceToNow(
                                                            new Date(
                                                                post.created_at,
                                                            ),
                                                            {
                                                                addSuffix: true,
                                                                locale: it,
                                                            },
                                                        )}
                                                        • da {post.author.name}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div
                                                    class="flex items-center justify-center gap-1.5 text-muted-foreground"
                                                    title="Numero di lettori unici"
                                                >
                                                    <EyeIcon class="size-3.5" />
                                                    <span
                                                        class="text-xs font-medium"
                                                        >{post.readers_count ||
                                                            0}</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div
                                                    class="flex items-center justify-center gap-1"
                                                >
                                                    {#if post.featured_image_url}
                                                        <div
                                                            class="size-7 rounded border bg-muted overflow-hidden"
                                                            title="Immagine in evidenza"
                                                        >
                                                            <img
                                                                src={post.featured_image_url}
                                                                alt=""
                                                                class="size-full object-cover"
                                                            />
                                                        </div>
                                                    {/if}
                                                    {#if post.attachment_url}
                                                        <div
                                                            class="size-7 rounded border bg-muted flex items-center justify-center text-muted-foreground"
                                                            title={post.attachment_name}
                                                        >
                                                            <FileIcon
                                                                class="size-3.5"
                                                            />
                                                        </div>
                                                    {/if}
                                                    {#if !post.featured_image_url && !post.attachment_url}
                                                        <span
                                                            class="text-xs text-muted-foreground"
                                                            >-</span
                                                        >
                                                    {/if}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <div
                                                    class="flex items-center justify-end gap-0.5 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity"
                                                >
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        class="size-7"
                                                        href="/admin/committees/{committee.id}/posts/{post.id}/edit"
                                                        title="Modifica post"
                                                    >
                                                        <EditIcon
                                                            class="size-3.5"
                                                        />
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        class="size-7 text-destructive hover:text-destructive hover:bg-destructive/10"
                                                        onclick={() =>
                                                            handleDeletePost(
                                                                post.id,
                                                            )}
                                                        title="Elimina post"
                                                    >
                                                        <TrashIcon
                                                            class="size-3.5"
                                                        />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    {/each}
                                </tbody>
                            </table>
                        </div>
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>
    </div>

    <!-- Add Member Dialog -->
    <Dialog.Root bind:open={addMemberDialogOpen}>
        <Dialog.Content>
            <Dialog.Header>
                <Dialog.Title>Aggiungi Socio al Comitato</Dialog.Title>
                <Dialog.Description>
                    Seleziona un socio da aggiungere al comitato.
                </Dialog.Description>
            </Dialog.Header>

            <form
                onsubmit={(e) => {
                    e.preventDefault();
                    handleAddMember();
                }}
                class="space-y-4"
            >
                <div class="space-y-2">
                    <Label>Socio <span class="text-red-500">*</span></Label>
                    <div class="relative">
                        <Popover.Root bind:open={openCombobox}>
                            <Popover.Trigger>
                                {#snippet child({ props })}
                                    <Button
                                        variant="outline"
                                        role="combobox"
                                        {...props}
                                        class="w-full justify-between"
                                    >
                                        {selectedMemberId
                                            ? availableMembers.find(
                                                  (m) =>
                                                      m.id === selectedMemberId,
                                              )?.name
                                            : "Seleziona socio..."}
                                        <SelectorIcon
                                            class="ml-2 size-4 shrink-0 opacity-50"
                                        />
                                    </Button>
                                {/snippet}
                            </Popover.Trigger>
                            <Popover.Content
                                class="w-[--bits-popover-anchor-width] p-0"
                            >
                                <Command.Root>
                                    <Command.Input
                                        placeholder="Cerca socio..."
                                    />
                                    <Command.List>
                                        <Command.Empty
                                            >Nessun socio trovato.</Command.Empty
                                        >
                                        <Command.Group>
                                            {#each availableMembers as member}
                                                <Command.Item
                                                    value={member.name}
                                                    onSelect={() => {
                                                        selectedMemberId =
                                                            member.id;
                                                        openCombobox = false;
                                                    }}
                                                >
                                                    <CheckIcon
                                                        class={cn(
                                                            "mr-2 size-4",
                                                            selectedMemberId ===
                                                                member.id
                                                                ? "opacity-100"
                                                                : "opacity-0",
                                                        )}
                                                    />
                                                    {member.name}
                                                    {#if member.email}
                                                        <span
                                                            class="ml-2 text-xs text-muted-foreground"
                                                        >
                                                            {member.email}
                                                        </span>
                                                    {/if}
                                                </Command.Item>
                                            {/each}
                                        </Command.Group>
                                    </Command.List>
                                </Command.Root>
                            </Popover.Content>
                        </Popover.Root>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="role">Ruolo (opzionale)</Label>
                    <Input
                        id="role"
                        type="text"
                        bind:value={memberRole}
                        placeholder="es. Coordinatore, Membro, Segretario..."
                    />
                </div>

                <Dialog.Footer>
                    <Button
                        type="button"
                        variant="outline"
                        onclick={() => (addMemberDialogOpen = false)}
                    >
                        Annulla
                    </Button>
                    <Button type="submit" disabled={!selectedMemberId}
                        >Aggiungi</Button
                    >
                </Dialog.Footer>
            </form>
        </Dialog.Content>
    </Dialog.Root>

    <!-- Confirm Member Removal Dialog -->
    <Dialog.Root bind:open={confirmRemovalOpen}>
        <Dialog.Content>
            <Dialog.Header>
                <Dialog.Title>Rimuovi Membro</Dialog.Title>
                <Dialog.Description>
                    Sei sicuro di voler rimuovere <strong
                        >{memberToRemoveName}</strong
                    > dal comitato? Questa azione non può essere annullata.
                </Dialog.Description>
            </Dialog.Header>
            <Dialog.Footer>
                <Button
                    variant="outline"
                    onclick={() => (confirmRemovalOpen = false)}
                >
                    Annulla
                </Button>
                <Button variant="destructive" onclick={confirmRemoveMember}>
                    Rimuovi
                </Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>

    <!-- Confirm Post Deletion Dialog -->
    <Dialog.Root bind:open={confirmPostDeletionOpen}>
        <Dialog.Content>
            <Dialog.Header>
                <Dialog.Title>Elimina Post</Dialog.Title>
                <Dialog.Description>
                    Sei sicuro di voler eliminare questo post dalla bacheca?
                    Questa azione non può essere annullata.
                </Dialog.Description>
            </Dialog.Header>
            <Dialog.Footer>
                <Button
                    variant="outline"
                    onclick={() => (confirmPostDeletionOpen = false)}
                >
                    Annulla
                </Button>
                <Button variant="destructive" onclick={confirmDeletePost}>
                    Elimina
                </Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>

    <!-- Confirm Image Deletion Dialog -->
    <Dialog.Root bind:open={confirmImageDeletionOpen}>
        <Dialog.Content>
            <Dialog.Header>
                <Dialog.Title>Rimuovi Logo Comitato</Dialog.Title>
                <Dialog.Description>
                    Sei sicuro di voler rimuovere il logo del comitato? Questa
                    azione non può essere annullata.
                </Dialog.Description>
            </Dialog.Header>
            <Dialog.Footer>
                <Button
                    variant="outline"
                    onclick={() => (confirmImageDeletionOpen = false)}
                >
                    Annulla
                </Button>
                <Button variant="destructive" onclick={confirmDeleteImage}>
                    Rimuovi
                </Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>
</AdminLayout>
