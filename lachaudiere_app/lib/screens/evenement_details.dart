import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:lachaudiere_app/models/evenement.dart';
import 'package:lachaudiere_app/providers/evenement_provider.dart';
import 'package:lachaudiere_app/providers/theme_provider.dart';
import 'package:flutter_markdown/flutter_markdown.dart';


class EvenementDetails extends StatefulWidget {
  final Evenement evenement;
  const EvenementDetails({super.key, required this.evenement});

  @override
  State<EvenementDetails> createState() => _EvenementDetailsState();
}

class _EvenementDetailsState extends State<EvenementDetails> {
  bool _isLoadingDetails = true;

  @override
  void initState() {
    super.initState();
    _loadDetails();
  }

  Future<void> _loadDetails() async {
    await Provider.of<EvenementProvider>(context, listen: false)
        .fetchAndApplyDetails(widget.evenement);
    if (mounted) {
      setState(() {
        _isLoadingDetails = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final evenement = widget.evenement;

    return Scaffold(
      appBar: AppBar(title: Text(evenement.titre), actions: [
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
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(evenement.titre, style: Theme.of(context).textTheme.headlineMedium),
            const SizedBox(height: 8),
            Chip(label: Text(evenement.categorie.libelle)),
            const Divider(height: 32),
            Text('Description', style: Theme.of(context).textTheme.headlineSmall),
            const SizedBox(height: 8),
            _isLoadingDetails
              ? const SizedBox(
                  height: 100,
                  child: Center(child: CircularProgressIndicator()),
                )
              : LayoutBuilder(
                  builder: (context, constraints) {
                    return ConstrainedBox(
                      constraints: BoxConstraints(
                        minHeight: 0,
                        maxHeight: double.infinity,
                      ),
                      child: MarkdownBody(
                        data: evenement.description ?? "Aucune description.",
                      ),
                    );
                  },
                ),

            const Divider(height: 32),
            Text('Images', style: Theme.of(context).textTheme.headlineSmall),
            const SizedBox(height: 8),
            _isLoadingDetails
                ? const SizedBox.shrink()
                : _buildImages(evenement),
          ],
        ),
      ),
    );
  }

  Widget _buildImages(Evenement evenement) {
    if (evenement.images.isEmpty) {
      return const Text("Aucune image pour cet événement.");
    }
    return Column(
      children: evenement.images.map((image) {
        return Card(
          clipBehavior: Clip.antiAlias,
          margin: const EdgeInsets.only(bottom: 16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Image.network(
                image.url,
                fit: BoxFit.cover,
                width: double.infinity,
                height: 200,
                loadingBuilder: (context, child, loadingProgress) {
                  if (loadingProgress == null) return child;
                  return const SizedBox(
                    height: 200,
                    child: Center(child: CircularProgressIndicator()),
                  );
                },
                errorBuilder: (context, error, stackTrace) {
                  print("Erreur de chargement pour ${image.url}: $error");
                  return const SizedBox(
                    height: 200,
                    child: Center(child: Icon(Icons.broken_image, size: 50, color: Colors.grey)),
                  );
                },
              ),
              if (image.legende.isNotEmpty)
                Padding(
                  padding: const EdgeInsets.all(12.0),
                  child: Text(image.legende, style: Theme.of(context).textTheme.bodyMedium),
                ),
            ],
          ),
        );
      }).toList(),
    );
  }
}