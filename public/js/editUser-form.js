       function toggleKelasInput() {
            const roleSelect = document.getElementById('role');
            const kelasGroup = document.getElementById('kelas_group');
            if (roleSelect.value === 'admin') {
                kelasGroup.classList.add('hidden');
            } else {
                kelasGroup.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleKelasInput();

            const kelasInput = document.getElementById('kelas');
            const suggestionsBox = document.getElementById('kelas_suggestions');

            const classesList = [];
            const majors = [
                { name: 'rpl', count: 10 },
                { name: 'dkv', count: 3 }
            ];
            const grades = ['X', 'XI', 'XII'];

            grades.forEach(grade => {
                majors.forEach(major => {
                    for (let i = 1; i <= major.count; i++) {
                        classesList.push(`${grade}-${major.name} ${i}`);
                    }
                });
            });

            if (kelasInput && suggestionsBox) {
                kelasInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().replace(/[\s-]/g, '');
                    suggestionsBox.innerHTML = '';
                    
                    if (!query) {
                        suggestionsBox.classList.add('hidden');
                        return;
                    }

                    const matches = classesList.filter(item => {
                        const normalizedItem = item.toLowerCase().replace(/[\s-]/g, '');
                        return normalizedItem.includes(query);
                    });

                    if (matches.length === 0) {
                        suggestionsBox.classList.add('hidden');
                        return;
                    }

                    matches.forEach(match => {
                        const row = document.createElement('div');
                        row.className = "px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 cursor-pointer transition-colors duration-150";
                        row.textContent = match;
                        row.addEventListener('click', function() {
                            kelasInput.value = match;
                            suggestionsBox.classList.add('hidden');
                        });
                        suggestionsBox.appendChild(row);
                    });

                    suggestionsBox.classList.remove('hidden');
                });

                document.addEventListener('click', function(e) {
                    if (!kelasInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                        suggestionsBox.classList.add('hidden');
                    }
                });

                kelasInput.addEventListener('focus', function() {
                    if (this.value) {
                        const event = new Event('input');
                        kelasInput.dispatchEvent(event);
                    }
                });
            }
        });
