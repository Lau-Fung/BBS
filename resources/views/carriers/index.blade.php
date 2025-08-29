@php
	function sort_link($field, $label) {
		$current = request('sort');
		$dir = $current === $field ? "-$field" : $field;
		$params = array_merge(request()->query(), ['sort' => $dir]);
		$url = url()->current().'?'.http_build_query($params);
		return "<a href=\"$url\" class=\"hover:underline flex items-center\">$label</a>";
	}
@endphp

<x-app-layout>
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

		<div class="mb-8">
			<div class="flex flex-col sm:flex-row sm:justify-between">
				<div class="mb-4 sm:mb-0">
					<h1 class="text-3xl font-bold text-gray-900 dark:text-white">Carriers</h1>
					<p class="mt-2 text-gray-600 dark:text-gray-400">Manage carrier inventory and activation</p>
				</div>
				<div class="flex flex-col sm:flex-row gap-3 sm:items-center">
					<a href="{{ route('carriers.create') }}" 
					   class="inline-flex items-center px-6 py-3 mb-2 bg-blue-600 hover:bg-blue-700 text-gray-700 font-medium rounded-lg transition-colors duration-200 shadow-sm">
						<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
						</svg>
						New Carrier
					</a>
				</div>
			</div>
		</div>

		<div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
			<div class="p-6">
				<form method="GET" class="space-y-4">
					<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
						<div class="md:col-span-2">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
							<input class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" type="text" name="filter[q]" value="{{ request('filter.q') }}" placeholder="Search name...">
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Trashed</label>
							<select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" name="filter[trashed]">
								<option value="">Active</option>
								<option value="with" @selected(request('filter.trashed')==='with')>With deleted</option>
								<option value="only" @selected(request('filter.trashed')==='only')>Only deleted</option>
							</select>
						</div>
					</div>
					<div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
						<button class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 bg-gray-100 font-medium rounded-lg transition-colors duration-200 shadow-sm">Apply Filters</button>
						<a href="{{ route('carriers.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors duration-200 shadow-sm">Reset Filters</a>
					</div>
				</form>
			</div>
		</div>

		<div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
			<div class="overflow-x-auto">
				<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 m-auto" style="width: 90%;">
					<thead class="bg-gray-50 dark:bg-gray-700">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
							<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
						</tr>
					</thead>
					<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
						@forelse($carriers as $c)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
								<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $c->name }}</td>
								<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $c->created_at->format('Y-m-d') }}</td>
								<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
									<div class="flex items-center justify-end space-x-2">
										<a class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" href="{{ route('carriers.edit',$c) }}" style="padding-right: 10px;">Edit</a>
										@if($c->trashed())
											<form action="{{ route('carriers.restore',$c->id) }}" method="POST" class="inline">@csrf
												<button class="text-amber-600 hover:text-amber-900 dark:text-amber-400 dark:hover:text-amber-300 ml-2">Restore</button>
											</form>
										@else
											<form action="{{ route('carriers.destroy',$c) }}" method="POST" class="inline" onsubmit="return confirm('Delete this carrier?')">
												@csrf @method('DELETE')
												<button class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 ml-2">Delete</button>
											</form>
										@endif
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="3" class="px-6 py-12 text-center">
									<div class="text-gray-500 dark:text-gray-400">
										<svg class="mx-auto h-9 w-9 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"></path>
										</svg>
										<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No carriers found</h3>
										<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new carrier.</p>
										<div class="mt-6">
											<a href="{{ route('carriers.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg bg-blue-600 hover:bg-blue-700 transition-colors duration-200">New Carrier</a>
										</div>
									</div>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>

		<div class="mt-6">
			{{ $carriers->links() }}
		</div>
	</div>
</x-app-layout>

