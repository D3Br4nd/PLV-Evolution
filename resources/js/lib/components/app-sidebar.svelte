<script>
	import DashboardIcon from "@tabler/icons-svelte/icons/dashboard";
	import FolderIcon from "@tabler/icons-svelte/icons/folder";
	import InnerShadowTopIcon from "@tabler/icons-svelte/icons/inner-shadow-top";
	import UsersIcon from "@tabler/icons-svelte/icons/users";
	import CalendarIcon from "@tabler/icons-svelte/icons/calendar";
	import FileDescriptionIcon from "@tabler/icons-svelte/icons/file-description";
	import BellIcon from "@tabler/icons-svelte/icons/bell";
	import NavDocuments from "./nav-documents.svelte";
	import NavMain from "./nav-main.svelte";
	import NavUser from "./nav-user.svelte";
	import * as Sidebar from "@/lib/components/ui/sidebar/index.js";
	import { sidebarMenuButtonVariants } from "@/lib/components/ui/sidebar/sidebar-menu-button.svelte";
	import { cn } from "@/lib/utils.js";
	import { page } from "@inertiajs/svelte";

	const data = {
		user: null,
		navMain: [
			{
				title: "Dashboard",
				url: "/admin/dashboard",
				icon: DashboardIcon,
			},
			{
				title: "Soci",
				url: "/admin/members",
				icon: UsersIcon,
			},
			{
				title: "Comitati",
				url: "/admin/committees",
				icon: UsersIcon,
			},
			{
				title: "Eventi",
				url: "/admin/events",
				icon: CalendarIcon,
			},
			{
				title: "Progetti",
				url: "/admin/projects",
				icon: FolderIcon,
			},
			{
				title: "Contenuti",
				url: "/admin/content-pages",
				icon: FileDescriptionIcon,
			},
			{
				title: "Notifiche Broadcast",
				url: "/admin/broadcasts",
				icon: BellIcon,
			},
		],
		documents: [],
	};

	let { ...restProps } = $props();

	let user = $derived($page.props.auth?.user);
</script>

<Sidebar.Root collapsible="offcanvas" {...restProps}>
	<Sidebar.Header>
		<Sidebar.Menu>
			<Sidebar.MenuItem>
				<a
					href="/admin/dashboard"
					class={cn(
						sidebarMenuButtonVariants({
							size: "lg",
							variant: "default",
						}),
						"data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground",
					)}
					data-sidebar="menu-button"
					data-size="lg"
				>
					<div
						class="flex aspect-square size-8 items-center justify-center rounded-lg overflow-hidden"
					>
						<img
							src="/logo.png"
							alt="Pro Loco Logo"
							class="size-full object-cover"
						/>
					</div>
					<div class="grid flex-1 text-left text-sm leading-tight">
						<span class="truncate font-semibold"
							>Pro Loco Venticanese</span
						>
						<span class="truncate text-xs">Evolution</span>
					</div>
				</a>
			</Sidebar.MenuItem>
		</Sidebar.Menu>
	</Sidebar.Header>
	<Sidebar.Content>
		<NavMain items={data.navMain} />
		<NavDocuments items={data.documents} />
	</Sidebar.Content>
	<Sidebar.Footer>
		<NavUser {user} />
	</Sidebar.Footer>
</Sidebar.Root>
