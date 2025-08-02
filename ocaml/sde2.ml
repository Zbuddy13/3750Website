(* James Shaw CPSC 3520 SDE2 8/2/25 *)

(* Function checks and returns the first duplicate number in the list *)
(* Working with test cases *)
let first_duplicate lst =
  (* Checks if element x exists later in the list xs *)
  let rec has_duplicate_later x = function
  (* Case when list is empty *)
    | [] -> false 
  (* If y = x then duplicate is found *)
    | y :: _ when y = x -> true
  (* Continue searching the list for duplicates *)
    | _ :: xs -> has_duplicate_later x xs
  in
  (* iterates through the list to find the first element that has a duplicate later in the list *)
  let rec find_first = function
  (* If no duplicates then return -10000 *)
    | [] -> -10000
    | x :: xs ->
    (* If x has a duplicate later in xs, then x is the first duplicate *)
        if has_duplicate_later x xs then x
    (* Else keep iterating *)
        else find_first xs
  in
  (* Start the search for the first duplicate *)
  find_first lst

(* Function checks the list to see if the sum v can be made with list a and b *)
(* Working with test cases *)
let sumOfTwo (a, b, v) =
  (* Checks if there are any element y in ys such that x + y = v *)
  let rec check_with_b x = function
    | [] -> false
    | y :: ys -> if x + y = v then true else check_with_b x ys
  in
  (* Iterates through list a and for each element, checks if it sums with an element from b to v *)
  let rec check_a = function
    | [] -> false
    | x :: xs -> if check_with_b x b then true else check_a xs
  in
  (* Start the process with list a *)
  check_a a 

(* Checks for the frequency of each unique element in a list *)
(* Working with test cases *)
let frequency lst =
  (* Local reverse function to avoid using built in *)
  let rec reverse_aux acc = function
    | [] -> acc
    (* Prepend x to acc and recurse *)
    | x :: xs -> reverse_aux (x :: acc) xs
  in
  (* Count occurrences of x in a list *)
  let rec count x = function
    | [] -> 0
    | y :: ys -> if x = y then 1 + count x ys else count x ys
  in
  (* Gets unique elements from a list without using List.filter *)
  let rec unique_elements_aux acc = function
  (* Once list is empty, reverse acc to maintain original order of first appearance *)
    | [] -> reverse_aux [] acc
    | x :: xs ->
      (* If x is already in acc meaning its unique *)
        if List.mem x acc then
        (* Skip x and continue *)
          unique_elements_aux acc xs
        else
        (* If x is new, add it to acc and continue *)
          unique_elements_aux (x :: acc) xs
  in
  (* Maps unique elements to their (element, count) pairs without using List.map *)
  let rec map_to_pairs acc = function
  (* When unique elements are processed, reverse acc to maintain proper order *)
    | [] -> reverse_aux [] acc
  (* Create pair (x, count of x in original lst), add to acc, and recurse *)
    | x :: xs -> map_to_pairs ((x, count x lst) :: acc) xs
  in
  (* Get unique elements and then map them to their frequencies *)
  map_to_pairs [] (unique_elements_aux [] lst)

(* Function returns n number of elements from the list given *)
(* Working with test cases *)
let take (n, lst) =
  (* Local reverse function to avoid using built in *)
  let rec reverse_aux acc = function
    | [] -> acc
    | x :: xs -> reverse_aux (x :: acc) xs
  in
  (* Recursively takes n elements from the list and accumulate them in acc *)
  let rec take_aux n acc = function
  (* If empty return accumulated list *)
    | [] -> reverse_aux [] acc 
  (* If n is 0 or less, stop taking and return accumulated elements *)
    | _ when n <= 0 -> reverse_aux [] acc
  (* Decrement n, add x to acc, and recurse with rest of list *)
    | x :: xs -> take_aux (n - 1) (x :: acc) xs
  in
  (* Start taking n elements from the list *)
  take_aux n [] lst

(* Function takes a list and returns a set of all the possible subsets *)
(* Uses merge sort to sort the final list from [] to [1; 2; 3; 4] *)
(* Test cases working *)
let powerset lst =
  (* Local reverse function to avoid using built in *)
  let rec reverse_aux acc = function
    | [] -> acc
  (* Increment count and recurse for rest of list *)
    | x :: xs -> reverse_aux (x :: acc) xs
  in
  (* Length function for a list (finds out how many elements in the list)*)
  (* Used to sort the elements in proper order *)
  let rec length_custom = function
    | [] -> 0
    | _ :: xs -> 1 + length_custom xs
  in
  (* List compare function, Returns 0 if equal, -1 if list1 < list2, 1 if list1 > list2 *)
  let rec compare_list list1 list2 =
    match (list1, list2) with
    | [], [] -> 0
    | [], _ -> -1
    | _, [] -> 1
    | x1 :: xs1, x2 :: xs2 ->
      (* Compare head elements *)
        let c = compare x1 x2 in
      (* If heads are different, return their comparison result *)
        if c <> 0 then c
      (* If heads are equal, compare the rest of the lists *)
        else compare_list xs1 xs2
  in
  (* Merge function for merge sort, using a comparison function *)
  let rec merge compare_func l1 l2 =
    match (l1, l2) with
    (* If l1 empty return l2 *)
    | [], _ -> l2
    (* If l2 empty return l1 *)
    | _, [] -> l1
    | x1 :: xs1, x2 :: xs2 ->
      (* If x1 comes before or is equal to x2 *)
        if compare_func x1 x2 <= 0 then 
      (* Take x1 and merge remaining of l1 with l2 *)
          x1 :: (merge compare_func xs1 l2)
        else
      (* Take x2 and merge l1 with remaining of l2 *)
          x2 :: (merge compare_func l1 xs2)
  in
  (* Splits a list into two halves for merge sort *)
  let rec split_list acc1 acc2 = function
  (* Return reversed accumulated halves *)
    | [] -> (reverse_aux [] acc1, reverse_aux [] acc2)
  (* If single element, add to first acc *)
    | [x] -> (reverse_aux [] (x :: acc1), reverse_aux [] acc2)
  (* Add x1 to acc1, x2 to acc2, recurse with rest to form the halfs *)
    | x1 :: x2 :: xs -> split_list (x1 :: acc1) (x2 :: acc2) xs
  in
  (* Merge sort function using a comparison function defined above*)
  let rec merge_sort_custom compare_func lst_to_sort = 
    match lst_to_sort with
  (* Empty list is already sorted *)
    | [] -> []
  (* Single element list is already sorted *)
    | [_] -> lst_to_sort
    | _ ->
      (* Split list into two halves *)
        let (left, right) = split_list [] [] lst_to_sort in
      (* Recursively sort left half *)
        let sorted_left = merge_sort_custom compare_func left in
      (* Recursively sort right half *)
        let sorted_right = merge_sort_custom compare_func right in
      (* Merge the sorted halves *)
        merge compare_func sorted_left sorted_right
  in
  (* Function to generate all subsets. acc holds the subsets found so far *)
  let rec generate_subsets_recursive acc = function
    (* Base case: if list is empty, return accumulated subsets *)
    | [] -> acc
    | x :: xs ->
      (* Helper function to create new subsets by adding the current element x to each existing subset in acc *)
        let rec create_new_subsets current_new_subsets_acc = function
        (* Reverse accumulated new subsets *)
          | [] -> reverse_aux [] current_new_subsets_acc
          | subset :: remaining_subsets ->
            (* Append x to current subset, add to accumulator, and recurse *)
              create_new_subsets ((subset @ [x]) :: current_new_subsets_acc) remaining_subsets
        in
        (* Combine newly created subsets (with x added) with the existing subsets (without x) *)
        generate_subsets_recursive (create_new_subsets [] acc @ acc) xs
  in
  (* Function to compare subsets by length, then numerically so they will be in proper order *)
  let rec subset_compare s1 s2 =
    let len_s1 = length_custom s1 in
    let len_s2 = length_custom s2 in
    (* Compare by length first *)
    if len_s1 <> len_s2 then len_s1 - len_s2
    (* Then compare by number order *)
    else compare_list s1 s2
  in
  (* Generate all subsets (starting with [[]] for the empty set), then sort them so they are returned in order*)
  merge_sort_custom subset_compare (generate_subsets_recursive [[]] lst)