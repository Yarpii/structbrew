<?php
$placementLabels = $placements ?? [];
$previewLinks = $previewPaths ?? [];
?>

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage ad placements, schedule duration, UTM tags, and click performance.</p>
    <button onclick="document.getElementById('createAdModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Ad
    </button>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <h3 class="text-sm font-semibold text-gray-800 mb-3">Placement Preview</h3>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-2">
        <?php foreach ($placementLabels as $key => $label): ?>
            <a href="<?= htmlspecialchars((string) ($previewLinks[$key] ?? '/')) ?>" target="_blank"
               class="rounded-md border border-gray-200 px-3 py-2 text-xs text-gray-700 hover:border-blue-300 hover:bg-blue-50 transition-colors">
                <span class="font-semibold"><?= htmlspecialchars((string) $key) ?></span><br>
                <span class="text-gray-500"><?= htmlspecialchars((string) $label) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php if (!empty($flashSuccess)): ?>
<div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm"><?= htmlspecialchars($flashSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Ad</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Placement</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">UTM</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Clicks</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Active</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach (($ads ?? []) as $ad): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 align-top">
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars((string) ($ad['name'] ?? '')) ?></p>
                        <p class="text-xs text-gray-500 mt-0.5"><?= htmlspecialchars((string) ($ad['title'] ?? '')) ?></p>
                        <p class="text-[11px] text-gray-400 mt-1">Order: <?= (int) ($ad['sort_order'] ?? 0) ?></p>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-600 align-top">
                        <?= htmlspecialchars($placementLabels[$ad['placement']] ?? (string) $ad['placement']) ?>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-500 align-top">
                        <?= htmlspecialchars((string) ($ad['utm_campaign'] ?? '—')) ?><br>
                        <span class="text-gray-400"><?= htmlspecialchars((string) ($ad['utm_source'] ?? '')) ?> <?= htmlspecialchars((string) ($ad['utm_medium'] ?? '')) ?></span>
                    </td>
                    <td class="px-5 py-3 text-center text-gray-700 align-top">
                        <span class="font-semibold"><?= (int) ($ad['clicks_count'] ?? 0) ?></span>
                        <?php if (!empty($ad['last_clicked_at'])): ?>
                            <div class="text-[11px] text-gray-400 mt-1"><?= htmlspecialchars((string) $ad['last_clicked_at']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 text-center align-top">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= (int) ($ad['is_active'] ?? 0) ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                            <?= (int) ($ad['is_active'] ?? 0) ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right align-top">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openEditAd(<?= htmlspecialchars(json_encode($ad), ENT_QUOTES) ?>)"
                                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-md transition-colors">Edit</button>
                            <form method="POST" action="/admin/marketing/ads/<?= (int) $ad['id'] ?>/delete" onsubmit="return confirm('Delete this ad?')">
                                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-md transition-colors">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($ads)): ?>
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No ads configured yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="createAdModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-6 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Add Ad</h3>
            <button onclick="document.getElementById('createAdModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">×</button>
        </div>

        <form method="POST" action="/admin/marketing/ads" class="space-y-4">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Internal Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Placement *</label>
                    <select name="placement" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <?php foreach ($placementLabels as $key => $label): ?>
                            <option value="<?= htmlspecialchars((string) $key) ?>"><?= htmlspecialchars((string) $label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input type="text" name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Subtitle</label><input type="text" name="subtitle" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">CTA Label</label><input type="text" name="cta_label" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">CTA URL</label><input type="text" name="cta_url" placeholder="/shop or https://..." class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Image URL</label><input type="text" name="image_url" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Background Value</label><input type="text" name="background_value" placeholder="#0f172a or linear-gradient(...)" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
            </div>

            <div class="grid grid-cols-5 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">UTM Source</label><input type="text" name="utm_source" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">UTM Medium</label><input type="text" name="utm_medium" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">UTM Campaign</label><input type="text" name="utm_campaign" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">UTM Term</label><input type="text" name="utm_term" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">UTM Content</label><input type="text" name="utm_content" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
            </div>

            <div class="grid grid-cols-4 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Starts At</label><input type="datetime-local" name="starts_at" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Ends At</label><input type="datetime-local" name="ends_at" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label><input type="number" name="sort_order" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Active</label><select name="is_active" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"><option value="1">Yes</option><option value="0">No</option></select></div>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('createAdModal').classList.add('hidden')" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Create</button>
            </div>
        </form>
    </div>
</div>

<div id="editAdModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-6 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Edit Ad</h3>
            <button onclick="document.getElementById('editAdModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">×</button>
        </div>

        <form id="editAdForm" method="POST" class="space-y-4">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Internal Name *</label><input type="text" name="name" id="editName" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Placement *</label><select name="placement" id="editPlacement" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"><?php foreach ($placementLabels as $key => $label): ?><option value="<?= htmlspecialchars((string) $key) ?>"><?= htmlspecialchars((string) $label) ?></option><?php endforeach; ?></select></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input type="text" name="title" id="editTitle" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Subtitle</label><input type="text" name="subtitle" id="editSubtitle" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">CTA Label</label><input type="text" name="cta_label" id="editCtaLabel" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">CTA URL</label><input type="text" name="cta_url" id="editCtaUrl" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Image URL</label><input type="text" name="image_url" id="editImageUrl" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Background Value</label><input type="text" name="background_value" id="editBackgroundValue" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
            </div>
            <div class="grid grid-cols-5 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">UTM Source</label><input type="text" name="utm_source" id="editUtmSource" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">UTM Medium</label><input type="text" name="utm_medium" id="editUtmMedium" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">UTM Campaign</label><input type="text" name="utm_campaign" id="editUtmCampaign" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">UTM Term</label><input type="text" name="utm_term" id="editUtmTerm" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">UTM Content</label><input type="text" name="utm_content" id="editUtmContent" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
            </div>
            <div class="grid grid-cols-4 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Starts At</label><input type="datetime-local" name="starts_at" id="editStartsAt" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Ends At</label><input type="datetime-local" name="ends_at" id="editEndsAt" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label><input type="number" name="sort_order" id="editSortOrder" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Active</label><select name="is_active" id="editIsActive" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"><option value="1">Yes</option><option value="0">No</option></select></div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editAdModal').classList.add('hidden')" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-gray-900 hover:bg-black text-white text-sm font-medium rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function toInputDateTime(value) {
    if (!value) return '';
    return String(value).slice(0, 16).replace(' ', 'T');
}

function openEditAd(ad) {
    document.getElementById('editName').value = ad.name || '';
    document.getElementById('editPlacement').value = ad.placement || '';
    document.getElementById('editTitle').value = ad.title || '';
    document.getElementById('editSubtitle').value = ad.subtitle || '';
    document.getElementById('editCtaLabel').value = ad.cta_label || '';
    document.getElementById('editCtaUrl').value = ad.cta_url || '';
    document.getElementById('editImageUrl').value = ad.image_url || '';
    document.getElementById('editBackgroundValue').value = ad.background_value || '';
    document.getElementById('editUtmSource').value = ad.utm_source || '';
    document.getElementById('editUtmMedium').value = ad.utm_medium || '';
    document.getElementById('editUtmCampaign').value = ad.utm_campaign || '';
    document.getElementById('editUtmTerm').value = ad.utm_term || '';
    document.getElementById('editUtmContent').value = ad.utm_content || '';
    document.getElementById('editStartsAt').value = toInputDateTime(ad.starts_at);
    document.getElementById('editEndsAt').value = toInputDateTime(ad.ends_at);
    document.getElementById('editSortOrder').value = ad.sort_order || 0;
    document.getElementById('editIsActive').value = String(ad.is_active ?? 0);
    document.getElementById('editAdForm').action = '/admin/marketing/ads/' + ad.id;
    document.getElementById('editAdModal').classList.remove('hidden');
}
</script>
