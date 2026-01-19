<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { router } from "@inertiajs/svelte";
    import { onMount } from "svelte";
    import ChevronLeft from "lucide-svelte/icons/chevron-left";
    import ChevronRight from "lucide-svelte/icons/chevron-right";
    import { Button } from "@/lib/components/ui/button";
    import { Input } from "@/lib/components/ui/input";
    import { Badge } from "@/lib/components/ui/badge";
    import * as Card from "@/lib/components/ui/card";
    import * as Dialog from "@/lib/components/ui/dialog";
    import * as Select from "@/lib/components/ui/select";

    import { Textarea } from "@/lib/components/ui/textarea";
    import { Label } from "@/lib/components/ui/label";

    /** @type {{ events: any[], currentDate: string, filterType: string|null, committees: any[] }} */
    let { events, currentDate, filterType = null, committees = [] } = $props();

    // State for Date Navigation
    let current = $derived(new Date(currentDate));
    let headerTitle = $derived.by(
        () =>
            `Eventi · ${current.toLocaleString("it-IT", { month: "long", year: "numeric" })}`,
    );

    // Calendar Generation Logic
    let days = $derived.by(() => {
        const year = current.getFullYear();
        const month = current.getMonth();

        const firstDayOfMonth = new Date(year, month, 1);
        const lastDayOfMonth = new Date(year, month + 1, 0);

        const startDay =
            firstDayOfMonth.getDay() === 0 ? 6 : firstDayOfMonth.getDay() - 1; // Adjust for Monday start
        const endDay = lastDayOfMonth.getDate();

        const totalCells = Math.ceil((startDay + endDay) / 7) * 7;

        let d = [];
        for (let i = 0; i < totalCells; i++) {
            if (i < startDay || i >= startDay + endDay) {
                d.push(null);
            } else {
                d.push(new Date(year, month, i - startDay + 1));
            }
        }
        return d;
    });

    function previousMonth() {
        const d = new Date(current);
        d.setMonth(d.getMonth() - 1);
        visit(d);
    }

    function nextMonth() {
        const d = new Date(current);
        d.setMonth(d.getMonth() + 1);
        visit(d);
    }

    function visit(date) {
        let year = date.getFullYear();
        let month = date.getMonth() + 1;
        const params = { year, month };

        if (filterType) {
            params.type = filterType;
        }

        router.get("/admin/events", params, { preserveState: false });
    }

    function setFilter(type) {
        const params = {
            year: current.getFullYear(),
            month: current.getMonth() + 1,
        };

        if (type) {
            params.type = type;
        }

        router.get("/admin/events", params, { preserveState: false });
    }

    function clearFilter() {
        setFilter(null);
    }

    function getEventsForDay(date) {
        if (!date) return [];

        // Target date in local time string "YYYY-MM-DD"
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        const targetDateStr = `${year}-${month}-${day}`;

        return events.filter((e) => {
            // Convert UTC strings to Local Date objects
            const startDate = new Date(e.start_date);
            const endDate = new Date(e.end_date);

            // Format to "YYYY-MM-DD" in Local Time
            const startStr = `${startDate.getFullYear()}-${String(startDate.getMonth() + 1).padStart(2, "0")}-${String(startDate.getDate()).padStart(2, "0")}`;
            const endStr = `${endDate.getFullYear()}-${String(endDate.getMonth() + 1).padStart(2, "0")}-${String(endDate.getDate()).padStart(2, "0")}`;

            // Check overlap
            return targetDateStr >= startStr && targetDateStr <= endStr;
        });
    }

    // Get color class for event type
    function getEventColor(type) {
        const colors = {
            meeting: "bg-blue-500 text-white",
            event: "bg-green-500 text-white",
            fair: "bg-purple-500 text-white",
            other: "bg-gray-500 text-white",
        };
        return colors[type] || colors.other;
    }

    // Dialog State
    let isNewEventOpen = $state(false);
    let isEditEventOpen = $state(false);
    let isDialogOpen = $state(false);
    let isDeleteConfirmOpen = $state(false);
    /** @type {any} */
    let selectedEvent = $state(null);
    let loading = $state(false);

    let eventForm = $state({
        id: null,
        title: "",
        start_date: "",
        end_date: "",
        start_time: "09:00",
        end_time: "18:00",
        type: "fair",
        description: "",
        committee_id: null,
    });

    function openNewEvent(date) {
        if (!date) return;
        // Correction: Use local time for YYYY-MM-DD format to avoid timezone shifts
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        const dateStr = `${year}-${month}-${day}`;

        eventForm = {
            id: null,
            title: "",
            start_date: dateStr,
            end_date: dateStr,
            start_time: "09:00",
            end_time: "18:00",
            type: "meeting",
            description: "",
            committee_id: null,
        };
        isNewEventOpen = true;
        isEditEventOpen = false;
        isDialogOpen = true;
    }

    function openEditEvent(event, e) {
        e.stopPropagation();
        e.preventDefault();
        selectedEvent = event;

        // Helper to format Date to "YYYY-MM-DD" (Local)
        const toDateStr = (d) => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, "0");
            const day = String(d.getDate()).padStart(2, "0");
            return `${year}-${month}-${day}`;
        };

        // Helper to format Date to "HH:MM" (Local)
        const toTimeStr = (d) => {
            const hours = String(d.getHours()).padStart(2, "0");
            const mins = String(d.getMinutes()).padStart(2, "0");
            return `${hours}:${mins}`;
        };

        const startDate = new Date(event.start_date);
        const endDate = new Date(event.end_date);

        eventForm = {
            id: event.id,
            title: event.title,
            start_date: toDateStr(startDate),
            end_date: toDateStr(endDate),
            start_time: toTimeStr(startDate),
            end_time: toTimeStr(endDate),
            type: event.type,
            description: event.description || "",
            committee_id: event.committee_id || "none",
        };
        isEditEventOpen = true;
        isNewEventOpen = false;
        isDialogOpen = true;
    }

    function closeDialogs() {
        isNewEventOpen = false;
        isEditEventOpen = false;
        isDialogOpen = false;
        selectedEvent = null;
        loading = false;
    }

    function submitEvent() {
        loading = true;
        const data = {
            title: eventForm.title,
            type: eventForm.type,
            start_date: `${eventForm.start_date} ${eventForm.start_time}:00`,
            end_date: `${eventForm.end_date} ${eventForm.end_time}:00`,
            description: eventForm.description,
            committee_id:
                eventForm.committee_id === "none"
                    ? null
                    : eventForm.committee_id,
        };

        if (eventForm.id) {
            router.put(`/admin/events/${eventForm.id}`, data, {
                onSuccess: closeDialogs,
                onFinish: () => (loading = false),
            });
        } else {
            router.post("/admin/events", data, {
                onSuccess: closeDialogs,
                onFinish: () => (loading = false),
            });
        }
    }

    function requestDeleteEvent() {
        if (!selectedEvent) return;
        isDeleteConfirmOpen = true;
    }

    function confirmDeleteEvent() {
        if (!selectedEvent) return;

        loading = true;
        router.delete(`/admin/events/${selectedEvent.id}`, {
            onSuccess: () => {
                closeDialogs();
                isDeleteConfirmOpen = false;
            },
            onFinish: () => (loading = false),
        });
    }
</script>

<AdminLayout title="Calendario Eventi">
    {#snippet headerActions()}
        <!-- Empty header actions -->
    {/snippet}

    <div class="h-full flex flex-col space-y-6">
        <div
            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Calendario Eventi
                </h1>
                <p
                    class="text-sm text-muted-foreground uppercase tracking-wide font-medium"
                >
                    {current.toLocaleString("it-IT", {
                        month: "long",
                        year: "numeric",
                    })}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center gap-2 border rounded-md p-1 bg-background shadow-sm"
                >
                    <Button
                        variant="ghost"
                        size="icon"
                        class="size-8"
                        onclick={previousMonth}
                        aria-label="Mese precedente"
                        disabled={false}
                    >
                        <ChevronLeft class="size-4" />
                    </Button>
                    <div
                        class="text-xs font-bold px-2 min-w-[100px] text-center capitalize"
                    >
                        {current.toLocaleString("it-IT", { month: "short" })}
                    </div>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="size-8"
                        onclick={nextMonth}
                        aria-label="Mese successivo"
                        disabled={false}
                    >
                        <ChevronRight class="size-4" />
                    </Button>
                </div>
                <Button
                    onclick={() => openNewEvent(new Date())}
                    class="shadow-sm"
                    disabled={false}
                >
                    Nuovo evento
                </Button>
            </div>
        </div>

        <!-- Toolbar con navigazione e filtro -->
        <div class="flex items-center justify-between gap-4 pb-4 border-b">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-muted-foreground"
                        >Filtra per tipo:</span
                    >
                    <Select.Root
                        type="single"
                        value={filterType || "all"}
                        onValueChange={(v) => setFilter(v === "all" ? null : v)}
                    >
                        <Select.Trigger class="w-[180px] h-9">
                            <span data-slot="select-value" class="truncate">
                                {filterType === "all" || !filterType
                                    ? "Tutti gli eventi"
                                    : {
                                          meeting: "Riunione",
                                          event: "Evento",
                                          fair: "Fiera",
                                          other: "Altro",
                                      }[filterType]}
                            </span>
                        </Select.Trigger>
                        <Select.Content>
                            <Select.Item value="all"
                                >Tutti gli eventi</Select.Item
                            >
                            <Select.Item value="meeting">Riunione</Select.Item>
                            <Select.Item value="event">Evento</Select.Item>
                            <Select.Item value="fair">Fiera</Select.Item>
                            <Select.Item value="other">Altro</Select.Item>
                        </Select.Content>
                    </Select.Root>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div
            class="grid grid-cols-7 gap-px bg-border border border-border rounded-lg overflow-hidden flex-1"
        >
            <!-- Weekday Headers -->
            {#each ["Lun", "Mar", "Mer", "Gio", "Ven", "Sab", "Dom"] as day}
                <div
                    class="bg-card py-2 text-center text-xs font-semibold text-muted-foreground"
                >
                    {day}
                </div>
            {/each}

            <!-- Days -->
            {#each days as date}
                <div
                    class="bg-background min-h-[120px] p-2 relative group hover:bg-accent/40 transition cursor-pointer"
                    onclick={() => openNewEvent(date)}
                    role="button"
                    tabindex="0"
                    onkeydown={(e) => e.key === "Enter" && openNewEvent(date)}
                >
                    {#if date}
                        <span
                            class="text-sm font-medium {date.toDateString() ===
                            new Date().toDateString()
                                ? 'bg-primary text-primary-foreground rounded-full w-6 h-6 flex items-center justify-center'
                                : 'text-muted-foreground'}"
                        >
                            {date.getDate()}
                        </span>

                        <!-- Events List -->
                        <div class="mt-2 space-y-1">
                            {#each getEventsForDay(date) as event}
                                <div
                                    class="truncate cursor-pointer select-none"
                                    onclick={(e) => openEditEvent(event, e)}
                                    role="button"
                                    tabindex="0"
                                    onkeydown={(e) =>
                                        e.key === "Enter" &&
                                        openEditEvent(event, e)}
                                >
                                    <span
                                        class="text-xs px-2 py-1 rounded-md font-medium {getEventColor(
                                            event.type,
                                        )}"
                                    >
                                        {event.title}
                                    </span>
                                </div>
                            {/each}
                        </div>
                    {/if}
                </div>
            {/each}
        </div>
    </div>

    <!-- Dialog (Crea/Modifica) -->
    <Dialog.Root
        bind:open={isDialogOpen}
        onOpenChange={(open) => {
            if (!open) {
                closeDialogs();
            }
        }}
    >
        <Dialog.Content class="max-w-md">
            <Dialog.Header>
                <Dialog.Title>
                    {isEditEventOpen ? "Modifica evento" : "Nuovo evento"}
                </Dialog.Title>
                <Dialog.Description
                    >Inserisci i dettagli base.</Dialog.Description
                >
            </Dialog.Header>

            <div class="mt-4 space-y-3">
                <Input
                    bind:value={eventForm.title}
                    placeholder="Titolo"
                    type="text"
                />
                <div class="grid grid-cols-2 gap-2">
                    <label class="block">
                        <span class="text-xs text-muted-foreground mb-1 block"
                            >Data inizio</span
                        >
                        <Input bind:value={eventForm.start_date} type="date" />
                    </label>
                    <label class="block">
                        <span class="text-xs text-muted-foreground mb-1 block"
                            >Data fine</span
                        >
                        <Input bind:value={eventForm.end_date} type="date" />
                    </label>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <label class="block">
                        <span class="text-xs text-muted-foreground mb-1 block"
                            >Ora inizio</span
                        >
                        <Input bind:value={eventForm.start_time} type="time" />
                    </label>
                    <label class="block">
                        <span class="text-xs text-muted-foreground mb-1 block"
                            >Ora fine</span
                        >
                        <Input bind:value={eventForm.end_time} type="time" />
                    </label>
                </div>
                <select
                    bind:value={eventForm.type}
                    class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm"
                >
                    <option value="meeting">Riunione</option>
                    <option value="event">Evento</option>
                    <option value="fair">Fiera</option>
                    <option value="other">Altro</option>
                </select>

                <div class="grid gap-2">
                    <Label for="description">Testo Aggiuntivo</Label>
                    <Textarea
                        id="description"
                        bind:value={eventForm.description}
                        placeholder="Dettagli aggiuntivi..."
                        class="resize-none"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="committee">Comitato (Opzionale)</Label>
                    <select
                        id="committee"
                        bind:value={eventForm.committee_id}
                        class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm"
                    >
                        <option value="none">Nessun comitato</option>
                        {#each committees as committee}
                            <option value={committee.id}
                                >{committee.name}</option
                            >
                        {/each}
                    </select>
                    <p class="text-[0.8rem] text-muted-foreground">
                        Selezionando un comitato, la notifica verr&agrave;
                        inviata solo ai membri di quel comitato.
                    </p>
                </div>
            </div>

            <Dialog.Footer class="mt-6 justify-between sm:justify-between">
                {#if isEditEventOpen}
                    <Button
                        variant="destructive"
                        onclick={requestDeleteEvent}
                        disabled={loading}
                    >
                        Elimina
                    </Button>
                {:else}
                    <div></div>
                {/if}

                <div class="flex gap-2">
                    {#if isEditEventOpen && selectedEvent}
                        <Button
                            variant="outline"
                            href={`/admin/events/${selectedEvent.id}/checkins`}
                            onclick={() => {}}
                            disabled={false}
                        >
                            Check-in
                        </Button>
                    {/if}
                    <Dialog.Close>
                        {#snippet child({ props })}
                            <Button
                                {...props}
                                variant="outline"
                                disabled={loading}>Annulla</Button
                            >
                        {/snippet}
                    </Dialog.Close>
                    <Button onclick={submitEvent} disabled={loading}>
                        {loading ? "Salvataggio..." : "Salva"}
                    </Button>
                </div>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>

    <!-- Delete Confirmation Dialog -->
    <Dialog.Root bind:open={isDeleteConfirmOpen}>
        <Dialog.Content>
            <Dialog.Header>
                <Dialog.Title>Sei sicuro?</Dialog.Title>
                <Dialog.Description>
                    Questa azione non può essere annullata. L'evento verrà
                    eliminato permanentemente.
                </Dialog.Description>
            </Dialog.Header>
            <Dialog.Footer>
                <Button
                    variant="outline"
                    onclick={() => (isDeleteConfirmOpen = false)}
                >
                    Annulla
                </Button>
                <Button
                    variant="destructive"
                    onclick={confirmDeleteEvent}
                    disabled={loading}
                >
                    {loading ? "Eliminazione..." : "Elimina"}
                </Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Root>
</AdminLayout>
