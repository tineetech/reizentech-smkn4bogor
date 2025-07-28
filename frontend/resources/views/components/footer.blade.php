<footer class="footer footer-transparent d-print-none">
    <div class="container-xl">
        <div class="text-center align-items-center">
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">
                        Copyright &copy; {{ date('Y') > config('app.year_created') ? config('app.year_created') . ' - ' . date('Y') : config('app.year_created') }}
                        <a href="" class="link-secondary">{{ config('app.author_by') }}.</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
