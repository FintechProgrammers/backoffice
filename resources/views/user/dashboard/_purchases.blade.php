 <div class="card custom-card">
     <div class="card-header justify-content-between">
         <div class="card-title">
             Subscriptions
         </div>
     </div>
     <div class="card-body">
         <div class="table-responsive">
             <table class="table text-nowrap table-bordered">
                 <thead>
                     <tr>
                         <th scope="col">Service</th>
                         <th scope="col">End Date</th>
                         <th scope="col">Status</th>
                     </tr>
                 </thead>
                 <tbody id="content">
                     @forelse (auth()->user()->subscriptions as $item)
                         <tr>
                             <td>
                                 <x-package-title title="{{ $item->service->name }}" image="{{ $item->service->image }}"
                                     price="{{ $item->service->price }}" />
                             </td>
                             <td>
                                 {{ $item->end_date->format('jS,M Y H:i A') }}
                             </td>
                             <td>
                                 @if ($item->is_active)
                                     <span class="badge bg-success-transparent">Active</span>
                                 @else
                                     <span class="badge bg-warning-transparent">Expired</span>
                                 @endif
                             </td>
                         </tr>
                     @empty
                         <tr>
                             <td colspan="3" class="text-center">
                                 <span class="text-warning">no subscription available</span>
                             </td>
                         </tr>
                     @endforelse
                 </tbody>
             </table>
         </div>
     </div>
     <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
 </div>
