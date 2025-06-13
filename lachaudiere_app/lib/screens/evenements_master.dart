import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:lachaudiere_app/models/categorie.dart';
import 'package:lachaudiere_app/providers/evenement_provider.dart';
import 'package:lachaudiere_app/screens/evenement_preview.dart';
import 'package:lachaudiere_app/screens/evenement_details.dart';
import 'package:lachaudiere_app/providers/theme_provider.dart';

class EvenementsMaster extends StatelessWidget {
  const EvenementsMaster({super.key});

  String _getSortOptionText(SortOption option) {
    switch (option) {
      case SortOption.title:
        return 'Trier par titre';
      case SortOption.dateAsc:
        return 'Trier par date (asc)';
      case SortOption.dateDesc:
        return 'Trier par date (desc)';
      case SortOption.category:
        return 'Trier par catégorie';
    }
  }

  @override
  Widget build(BuildContext context) {
    final evenementProvider = Provider.of<EvenementProvider>(context, listen: false);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Événements La Chaudière'),
        actions: [
          PopupMenuButton<SortOption>(
            icon: const Icon(Icons.sort),
            onSelected: (SortOption result) {
              evenementProvider.updateSort(result);
            },
            itemBuilder: (BuildContext context) => <PopupMenuEntry<SortOption>>[
              ...SortOption.values.map((option) {
                return PopupMenuItem<SortOption>(
                  value: option,
                  child: Text(_getSortOptionText(option)),
                );
              })
            ],
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => evenementProvider.refreshData(),
          ),
          IconButton(
            icon: Consumer<ThemeProvider>(
              builder: (context, themeProvider, _) {
                return Icon(
                  themeProvider.themeMode == ThemeMode.dark
                      ? Icons.sunny
                      : Icons.nightlight_round_rounded
                );
              },
            ),
            onPressed: () => Provider.of<ThemeProvider>(context, listen: false).toggleTheme(),
          )
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(56.0),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
            child: TextField(
              decoration: InputDecoration(
                hintText: 'Rechercher un événement...',
                prefixIcon: const Icon(Icons.search),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12.0)),
                filled: true,
                fillColor: Theme.of(context).inputDecorationTheme.fillColor ?? Theme.of(context).canvasColor,
              ),
              onChanged: (value) {
                evenementProvider.updateSearchQuery(value);
              },
            ),
          ),
        ),
      ),
      body: Column(
        children: [
          _buildCategoryFilters(),
          Expanded(
            child: Consumer<EvenementProvider>(
              builder: (context, provider, child) {
                final evenements = provider.evenements;
                if (provider.isLoading && evenements.isEmpty) {
                  return const Center(child: CircularProgressIndicator());
                }
                if (provider.errorMessage != null && evenements.isEmpty) {
                  return Center(child: Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Text("Erreur: ${provider.errorMessage}", textAlign: TextAlign.center),
                  ));
                }
                if (evenements.isEmpty) {
                  return const Center(child: Text('Aucun événement trouvé.'));
                }

                return ListView.builder(
                  itemCount: evenements.length,
                  itemBuilder: (context, index) {
                    final evenement = evenements[index];
                    return EvenementPreview(
                      evenement: evenement,
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => EvenementDetails(evenement: evenement),
                          ),
                        );
                      },
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCategoryFilters() {
    return Consumer<EvenementProvider>(
      builder: (context, provider, child) {
        if (provider.isLoading || provider.categories.isEmpty) {
          return const SizedBox.shrink();
        }
        return SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
          child: Row(
            children: [
              ChoiceChip(
                label: const Text('Toutes'),
                selected: provider.selectedCategory == null,
                onSelected: (selected) {
                  if (selected) {
                    provider.updateFilter(null);
                  }
                },
              ),
              const SizedBox(width: 8),
              ...provider.categories.map((categorie) {
                return Padding(
                  padding: const EdgeInsets.only(right: 8.0),
                  child: ChoiceChip(
                    label: Text(categorie.libelle),
                    selected: provider.selectedCategory?.id == categorie.id,
                    onSelected: (selected) {
                      if (selected) {
                        provider.updateFilter(categorie);
                      }
                    },
                  ),
                );
              }).toList(),
            ],
          ),
        );
      },
    );
  }
}