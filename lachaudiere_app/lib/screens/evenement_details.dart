import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:lachaudiere_app/models/evenement.dart';
import 'package:lachaudiere_app/providers/evenement_provider.dart';

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
      appBar: AppBar(title: Text(evenement.titre)),
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
                ? const Center(child: CircularProgressIndicator())
                : Text(
                    evenement.description ?? "Aucune description.",
                    style: Theme.of(context).textTheme.bodyLarge,
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
          child: Column(
            children: [
              Image.network(
                image.url,
                fit: BoxFit.cover,
                width: double.infinity,
                height: 200,
                errorBuilder: (context, error, stackTrace) => const Icon(Icons.broken_image, size: 50),
              ),
              if (image.legende.isNotEmpty)
                Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Text(image.legende, style: Theme.of(context).textTheme.titleSmall),
                ),
            ],
          ),
        );
      }).toList(),
    );
  }
}