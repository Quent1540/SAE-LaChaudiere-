import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:lachaudiere_app/providers/evenement_provider.dart';
import 'package:lachaudiere_app/screens/evenement_preview.dart';
import 'package:lachaudiere_app/screens/evenement_details.dart';
import 'package:lachaudiere_app/providers/theme_provider.dart';

class EvenementsMaster extends StatelessWidget {
  const EvenementsMaster({super.key});

  @override
  Widget build(BuildContext context) {
    final evenementProvider = Provider.of<EvenementProvider>(context);
    final evenements = evenementProvider.evenements;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Événements La Chaudière'),
        actions: [

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
                fillColor: Colors.white,
              ),
              onChanged: (value) {
                evenementProvider.updateSearchQuery(value);
              },
            ),
          ),
        ),
      ),
      body: Consumer<EvenementProvider>(
        builder: (context, provider, child) {
          final evenements = provider.evenements;

          if (provider.isLoading && evenements.isEmpty) {
            return const Center(child: CircularProgressIndicator());
          }
          if (provider.errorMessage != null && evenements.isEmpty) {
            return Center(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Text("Erreur: ${provider.errorMessage}", textAlign: TextAlign.center),
              ),
            );
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
    );
  }
}
